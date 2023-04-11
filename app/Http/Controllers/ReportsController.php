<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use DB;
use App\Models\Campaign;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function campaigns()
    {
        $stats = $dates = $indexCache = [];

        $this->campaigns = Auth::user()->campaigns()
            ->withTrashed()
            ->with('schedulesWithTrashed', 'schedulesWithTrashed.message')
            ->get();

        $start = now()->subDays(30);
        $end = now();

        foreach ($this->sent($start, $end) as $k => $day) {
            $date = (new Carbon($day->date))->format("M j (D)");

            [$index, $campaign, $schedule] = $this->statsDetails($day);

            if (empty($stats[$day->campaign_id][$index]['stats'][$date]['Sent'])) {
                $stats[$day->campaign_id][$index]['campaign'] = $campaign;
                $stats[$day->campaign_id][$index]['schedule'] = $schedule;
                $stats[$day->campaign_id][$index]['stats'][$date]['Sent'] = 0;
            }

            $stats[$day->campaign_id][$index]['stats'][$date]['Sent'] += $day->total;
        }

        foreach ($this->events($start, $end) as $k => $day) {
            $date = (new Carbon($day->date))->format("M j (D)");
            $name = config('constants.eventTypesDetails')[$day->type_id]['short'];

            [$index, $campaign, $schedule] = $this->statsDetails($day);

            if (empty($stats[$day->campaign_id][$index]['stats'][$date][$name])) {
                $stats[$day->campaign_id][$index]['campaign'] = $campaign;
                $stats[$day->campaign_id][$index]['schedule'] = $schedule;
                $stats[$day->campaign_id][$index]['stats'][$date][$name] = 0;
            }

            $stats[$day->campaign_id][$index]['stats'][$date][$name] += $day->total;
        }

        return Inertia::render('Reports/Campaign', compact(['stats']));
    }

    public function statsDetails($day)
    {
        $campaign = $this->campaigns->where('id', $day->campaign_id)->first();

        if ($campaign instanceof Campaign) {
            $schedule = $day->schedule_id > 0
                ? $campaign->schedulesWithTrashed->where('id', $day->schedule_id)->first()
                : null;
        }

        $triggerId = sprintf("%05d", $schedule->trigger_schedule_id ?? $day->schedule_id);
        $isTrigger = $schedule->is_trigger ?? 0;
        $scheduleId = sprintf("%05d", $day->schedule_id);
        $index = join("|", [$triggerId, $isTrigger, $scheduleId]);

        if ($isTrigger) {
            $trigger = $scheduleId > 0
                ? $campaign->schedulesWithTrashed->where('id', $scheduleId)->first()
                : null;

            $schedule = $trigger && $trigger->is_trigger
                ? $campaign->schedulesWithTrashed
                    ->where('trigger_schedule_id', $trigger->id)
                    ->whereNull('is_trigger')
                    ->first()
                : null;
        }

        if (! $schedule instanceof App\Schedule) {
            $schedule = $scheduleId > 0
                ? $campaign->schedulesWithTrashed->where('id', $scheduleId)->first()
                : null;
        }

        return [
            $index,
            $campaign,
            $schedule,
        ];
    }

    public function sent(Carbon $start, Carbon $end)
    {
        return DB::table('pushes')
            ->select([
                DB::raw('date( pushes.created_at ) as `date`'),
                'campaign_id',
                'schedule_id',
                'campaigns.type',
                'campaigns.name',
                DB::raw('count( * ) as total'),
            ])
            ->leftJoin('campaigns', 'campaigns.id', '=', 'pushes.campaign_id')
            ->where('pushes.user_id', Auth::id())
            ->where('campaigns.user_id', Auth::id())
            ->whereBetween('pushes.created_at', [$start->format("Y-m-d 00:00:00"), $end->format("Y-m-d 23:59:59")])
            ->groupBy(['date', 'campaign_id', 'schedule_id'])
            ->orderBy('date')
            ->orderBy('campaigns.type')
            ->orderBy('campaigns.name')
            ->get();
            // ->toArray();
            // ->toSql();
    }

    public function events(Carbon $start, Carbon $end)
    {
        $types = [
            config('constants.eventTypesArr')["notification-delivered"],
            config('constants.eventTypesArr')["notification-clicked"],
            config('constants.eventTypesArr')["notification-closed"],
            config('constants.eventTypesArr')["permission-denied"],
        ];

        return //Auth::user()->events()
            DB::table('events')
            ->select([
                DB::raw('date( events.created_at ) as `date`'),
                'type_id',
                'pushes.campaign_id',
                'pushes.schedule_id',
                'campaigns.type',
                'campaigns.name',
                DB::raw('count( * ) as total'),
            ])
            ->leftJoin('pushes', 'pushes.uuid', '=', 'events.uuid')
            ->leftJoin('campaigns', 'campaigns.id', '=', 'pushes.campaign_id')
            ->where('events.user_id', Auth::id())
            ->where('pushes.user_id', Auth::id())
            ->where('campaigns.user_id', Auth::id())
            ->whereIn('type_id', $types)
            ->whereBetween('events.created_at', [$start->format("Y-m-d 00:00:00"), $end->format("Y-m-d 23:59:59")])
            ->groupBy(['date', 'type_id', 'pushes.campaign_id', 'pushes.schedule_id'])
            ->orderBy('date')
            ->orderBy('campaigns.type')
            ->orderBy('campaigns.name')
            ->get();
            // ->toSql();
    }

    public function websites()
    {
        $types = [
            $subscribe = config('constants.eventTypesArr')["subscribe"],
            $unsubscribe = config('constants.eventTypesArr')["unsubscribe"],
            $visit = config('constants.eventTypesArr')["visit"],
            $delivered = config('constants.eventTypesArr')["notification-delivered"],
            $clicked = config('constants.eventTypesArr')["notification-clicked"],
        ];

        $start = (new Carbon())->subDays(30);
        $end = new Carbon();
        $websites = Auth::user()->websites()->withTrashed()->get()->keyBy('id');

        $stats = DB::table('events')
            ->select([
                'website_id',
                DB::raw("date( created_at ) as `date`"),
                DB::raw("sum( if(type_id = {$subscribe}, 1, 0) ) as subscribes"),
                DB::raw("sum( if(type_id = {$unsubscribe}, 1, 0) ) as unsubscribes"),
                DB::raw("sum( if(type_id = {$visit}, 1, 0) ) as visits"),
                DB::raw("sum( if(type_id = {$delivered}, 1, 0) ) as deliveries"),
                DB::raw("sum( if(type_id = {$clicked}, 1, 0) ) as clicks"),
                DB::raw("count( * ) as total"),
            ])
            ->where('events.user_id', Auth::id())
            ->whereIn('type_id', $types)
            ->whereBetween('created_at', [$start->format("Y-m-d 00:00:00"), $end->format("Y-m-d 23:59:59")])
            ->groupBy('website_id', 'date')
            ->orderBy('website_id')
            ->orderBy('date')
            ->get();

        $sent = DB::table('pushes')
            ->select([
                'website_id',
                DB::raw("date( sent_at ) as `date`"),
                DB::raw("sum( if(is_success = 1, 1, 0) ) as sent"),
                DB::raw("sum( if(is_success = 0, 1, 0) ) as failed"),
                DB::raw("sum( if(is_success = 2, 1, 0) ) as queued"),
            ])
            ->where('user_id', Auth::id())
            ->whereBetween('sent_at', [$start->format("Y-m-d 00:00:00"), $end->format("Y-m-d 23:59:59")])
            ->groupBy('website_id', 'date')
            ->orderBy('website_id')
            ->orderBy('date')
            ->get();

        $sent->map(function ($item) use (&$stats) {
            $find = $stats->where('website_id', $item->website_id)->where('date', $item->date);

            if ($find->isEmpty()) {
                $item->subscribes = 0;
                $item->unsubscribes = 0;
                $item->visits = 0;
                $item->deliveries = 0;
                $item->clicks = 0;
                $item->total = 0;

                $stats->push($item);

                return $item;
            }

            foreach ($find as $key => $val) {
                if (! isset($val->sent)) {
                    $stats[$key]->sent = $item->sent;
                }
            }

            return $item;
        });

        $stats = $stats->sortBy('website_id');

        return Inertia::render('Reports/Website', compact(['stats', 'websites']));
        // return view('reports.websites', compact(['stats', 'websites']));
    }
}
