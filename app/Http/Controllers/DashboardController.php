<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Inertia\Inertia;
use App\Models\Subscriber;

class DashboardController extends Controller
{
    public $timeframe = [
        "-1 Day",
        "-7 Days",
        "-14 Days",
        "-30 Days",
    ];

    public $daysBack = [
        1,
        7,
        14,
        30,
    ];

    public $colors = [
        'blue' => '#5E50F9',
        'indigo' => '#6610f2',
        'purple' => '#6a008a',
        'purple-light' => '#F2EDF3',
        'pink' => '#E91E63',
        'red' => '#f96868',
        'orange' => '#f2a654',
        'yellow' => '#f6e84e',
        'green' => '#38E5CB',
        'teal' => '#58d8a3',
        'cyan' => '#57c7d4',
        'white' => '#ffffff',
        'gray' => '#6c757d',
        'gray-dark' => '#3e4b5b',
        'gray-light' => '#aab2bd',
        'gray-lighter' => '#e8eff4',
        'gray-lightest' => '#e6e9ed',
        'black' => '#000000',
    ];

    public $gradient = [
        'blue' => 'linear-gradient(to right, #9289FB, #5E50F9)',
        'indigo' => 'linear-gradient(to right, #6610f2, #6610f2)',
        'purple' => 'linear-gradient(to right, #6a008a, #6a008a)',
        'purple-light' => 'linear-gradient(to right, #F2EDF3, #F2EDF3)',
        'pink' => 'linear-gradient(to right, #E91E63, #E91E63)',
        'red' => 'linear-gradient(to right, #f96868, #f96868)',
        'orange' => 'linear-gradient(to right, #f2a654, #f2a654)',
        'yellow' => 'linear-gradient(to right, #f6e84e, #f6e84e)',
        'green' => 'linear-gradient(to right, #38E5CB, #38E5CB)',
        'teal' => 'linear-gradient(to right, #58d8a3, #58d8a3)',
        'cyan' => 'linear-gradient(to right, #57c7d4, #57c7d4)',
        'gray' => 'linear-gradient(to right, #6c757d, #6c757d)',
        'gray-dark' => 'linear-gradient(to right, #0f1531, #0f1531)',
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->colors = collect($this->colors);
        $this->gradient = collect($this->gradient);
    }

    public function period(int $timeframe)
    {
        // cap min
        if ($timeframe == 0) {
            $timeframe = 1;
        }

        // cap max
        if (! array_key_exists($timeframe, $this->timeframe)) {
            $timeframe = 3;
        }

        $start = today()->subDays($this->daysBack[$timeframe]);
        $end = today()->subDay();

        return CarbonPeriod::since($start)->until($end);
    }

    public function dates($timeframe)
    {
        return [
            $date = date("Y-m-d", strtotime("{$this->timeframe[$timeframe]}")),
            date("Y-m-d", strtotime("{$this->timeframe[$timeframe]}", strtotime($date))),
        ];
    }

    public function index()
    {
        $websites = Auth::user()->websites()->get();
        return Inertia::render('Dashboard/Index', compact('websites'));
    }

    public function subscribers(int $subscribed, $timeframe = -1)
    {
        $query = Auth::user()->subscribers()
            ->where('subscribed', '=', ($subscribed ? 1 : 0));

        // show all when $timeframe = -1
        if (! array_key_exists($timeframe, $this->timeframe)) {
            return $query->count();
        }

        [$date, $prevDate] = $this->dates($timeframe);

        return $query->where('created_at', '>=', $date)->count();
    }

    public function subscribersChart(int $subscribed, int $timeframe = -1)
    {
        $dates = $stats = [];
        $period = $this->period($timeframe);

        foreach ($period as $i => $dt) {
            $date = $dt->format('Y-m-d');
            $dates[$i] = $dt->format("M j (D)");

            $stats[$i] = Auth::user()->subscribers()
                ->where('subscribed', '=', ($subscribed ? 1 : 0))
                ->whereBetween('created_at', ["{$date} 00:00:00", "{$date} 23:59:59"])
                ->count();
                // ->toSql();
        }

        return [
            'labels' => $dates,
            'datasets' => [[
                'label' => $subscribed ? 'Subscribers' : 'Unsubscribers',
                'backgroundColor' => 'rgba(255,255,255,.2)',
                'borderColor' => 'rgba(255,255,255,.55)',
                'data' => $stats
            ]]
        ];
    }

    public function push(int $timeframe = -1)
    {
        $query = Auth::user()->pushes();

        // show all when $timeframe = -1
        if (! array_key_exists($timeframe, $this->timeframe)) {
            return $query->count();
        }

        [$date, $prevDate] = $this->dates($timeframe);

        return $query->where('created_at', '>=', $date)->count();
    }

    public function pushChart(int $timeframe)
    {
        $dates = $stats = [];
        $period = $this->period($timeframe);

        foreach ($period as $i => $dt) {
            $date = $dt->format('Y-m-d');
            $dates[$i] = $dt->format("M j (D)");

            $stats[$i] = Auth::user()->pushes()
                ->whereBetween('created_at', ["{$date} 00:00:00", "{$date} 23:59:59"])
                ->count();
                // ->toSql();
        }

        return [
            'labels' => $dates,
            'datasets' => [[
                'label' => 'Push Notifications',
                'backgroundColor' => 'rgba(255,255,255,.2)',
                'borderColor' => 'rgba(255,255,255,.55)',
                'data' => $stats
            ]]
        ];
    }

    public function pushCount(int $timeframe = -1)
    {
        // show all when $timeframe = -1
        if (! array_key_exists($timeframe, $this->timeframe)) {
            return [
                Auth::user()->pushes()->count(),
                '',
            ];
        }

        [$date, $prevDate] = $this->dates($timeframe);

        $count = Auth::user()->pushes()->where('created_at', '>=', $date)->count();
        $prev = Auth::user()->pushes()->whereBetween('created_at', [$prevDate, $date])->count();

        return [
            $count,
            round($count ? $count * 100 / ($count + $prev) : 0),
        ];
    }

    public function clicks(int $timeframe = -1)
    {
        $clickedType = config('constants.eventTypesArr')["notification-clicked"];

        // show all when $timeframe = -1
        if (! array_key_exists($timeframe, $this->timeframe)) {
            return Auth::user()->events()->where('type_id', $clickedType)->count();
        }

        [$date, $prevDate] = $this->dates($timeframe);

        $count = Auth::user()->events()
            ->where('created_at', '>=', $date)
            ->where('type_id', $clickedType)
            ->count();

        return $count;
    }

    public function clicksChart(int $timeframe)
    {
        $dates = $stats = [];
        $period = $this->period($timeframe);

        foreach ($period as $i => $dt) {
            $date = $dt->format('Y-m-d');
            $dates[$i] = $dt->format("M j (D)");

            $stats[$i] = Auth::user()->pushes()
                ->whereBetween('created_at', ["{$date} 00:00:00", "{$date} 23:59:59"])
                ->count();
                // ->toSql();
        }

        return [
            'labels' => $dates,
            'datasets' => [[
                'label' => 'Push Notifications',
                'backgroundColor' => 'rgba(255,255,255,.2)',
                'borderColor' => 'rgba(255,255,255,.55)',
                'data' => $stats
            ]]
        ];
    }

    public function events()
    {
        $types = [
            $delivered = config('constants.eventTypesArr')["notification-delivered"],
            $clicked = config('constants.eventTypesArr')["notification-clicked"],
            $closed = config('constants.eventTypesArr')["notification-closed"],
            $denied = config('constants.eventTypesArr')["permission-denied"],
        ];

        $dates = $stats = [];

        foreach (range(-6, 0) as $i => $days) {
            $ts = strtotime("{$days} days");
            $date = date("Y-m-d", $ts);
            $dates[$i] = date("M j (D)", $ts);

            $query = Auth::user()->events()
                ->select('type_id', \DB::raw('count( * ) as total'))
                ->whereIn('type_id', $types)
                ->whereBetween('created_at', ["{$date} 00:00:00", "{$date} 23:59:59"])
                ->groupBy('type_id')
                ->get()->toArray();
                // ->toSql();

            foreach ($types as $typeId) {
                $name = config('constants.eventTypesDetails')[$typeId]['name'];

                if (! array_key_exists($name, $stats)) {
                    $stats[$name] = [];
                }

                if (! array_key_exists($i, $stats[$name])) {
                    $stats[$name][$i] = 0;
                }
            }

            foreach ($query as $row) {
                $name = config('constants.eventTypesDetails')[$row['type_id']]['name'];
                $stats[$name][$i] += $row['total'];
            }
        }

        return [
            'labels' => $dates,
            'data' => array_values($stats),
        ];
    }

    public function subscriptions()
    {
        $stats = Auth::user()->subscribers()
            ->select('subscribed', \DB::raw('count( * ) AS total'))
            ->groupBy('subscribed')
            ->get()->toArray();

        $totals = [
            0 => 0,
            1 => 0,
        ];

        foreach ($stats as $row) {
            $totals[$row['subscribed']] = $row['total'];
        }

        $sum = array_sum($totals);

        $subsPerc = $sum ? round(($totals[1] / $sum) * 100) : 0;

        return [
            'labels' => ['Subscribed', 'Unsubscribed'],
            'datasets' => [[
                'borderWidth' => 0,
                'data' => [
                    $totals[1], // subs
                    $totals[0], // unsubs
                ],
                'totals' => [
                    $subsPerc, // subs
                    100 - $subsPerc // unsubs
                ],
            ]]
        ];
    }

    public function browsers(int $field = 0)
    {
        $select = [
            0 => 'events.browser as browser',
            1 => 'events.platform as browser',
            2 => "if( events.is_desktop, 'Desktop',
                if( events.is_mobile, 'Mobile',
                if( events.is_tablet, 'Tablet',
                if( events.is_robot, 'Robot', 'Unknown')))) as browser",
        ];

        $stats = Subscriber::select(\DB::raw($select[$field]), \DB::raw('count(distinct subscribers.id) as total'))
            ->join('events', 'events.subscriber_id', '=', 'subscribers.id')
            ->groupBy(\DB::raw(1))
            ->orderBy('total', 'desc')
            ->get()->toArray();

        $sum = array_sum(array_column($stats, 'total'));

        if (count($stats) > 6) {
            $top5 = array_slice($stats, 0, 5);
            $others = array_slice($stats, 5);
            $others = [
                'browser' => 'Others',
                'total' => array_sum(array_column($others, 'total')),
                'perc' => array_sum(array_column($others, 'perc')),
            ];
            $stats = array_merge($top5, [$others]);
        }

        $stats = array_map(function($item) use ($sum) {
            $item['browser'] = ucfirst($item['browser']);
            $item['perc'] = round($item['total'] * 100 / $sum, 2);
            return $item;
        }, $stats);

        return [
            'labels' => array_column($stats, 'browser'),
            'datasets' => [[
                'borderWidth' => 0,
                'data' => array_column($stats, 'total'),
                'totals' => array_column($stats, 'perc'),
            ]]
        ];
    }
}
