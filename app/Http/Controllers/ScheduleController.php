<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Campaign;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function update(Request $request, Campaign $campaign)
    {
        $keepIds = [];

        if ($campaign->type == 'waterfall') {
            $delay = 0;
        }

        foreach ($request->get('schedules') as $i => $data) {
            $id = $data['id'] ?? null;

            switch ($data['reoccurring_frequency']) {
                case "hourly":
                    $data['hour_minute'] = date("i", strtotime($data['hour_minute']));
                    break;
            }

            $schedule = is_null($id) ? new Schedule($data) : $campaign->schedules()->find($id);

            $schedule->order = $i;
            $schedule->campaign_id = $campaign->id;
            $schedule->user_id = Auth::id();
            $schedule->delay = $data['delay'] ?? null;
            $schedule->scheduled_at = $data['scheduled_at'] ?? null;
            $schedule->reoccurring_frequency = $data['reoccurring_frequency'] ?? null;
            $schedule->hour_minute = $data['hour_minute'] ?? null;
            $schedule->day = $data['day'] ?? null;
            $schedule->is_trigger = $data['is_trigger'] ?? null;
            $schedule->trigger_schedule_id = $data['trigger_schedule_id'] ?? null;
            $schedule->message_id = $data['message_id'] ?? null;

            $schedule->save();
            $schedule->refresh();

            $trigger = $this->storeTrigger($i, $request, $campaign);

            if ($trigger instanceof Schedule) {
                $keepIds[] = $data['trigger_schedule_id'] = $trigger->id;
            } else {
                $data['trigger_schedule_id'] = null;
            }

            $keepIds[] = $schedule->id;
        }

        $this->cleanup($keepIds, $campaign);
    }

    public function storeTrigger(int $i, Request $request, Campaign $campaign)
    {
        if (! is_array($request->get('trigger_message_id'))
            || ! array_key_exists($i, $request->get('trigger_message_id'))
            || $request->get('trigger_schedule_id')[$i] < 0
        ) {
            return;
        }

        return $this->store(
            $campaign,
            $request->get('trigger_schedule_id')[$i],
            [
                'is_trigger' => 1,
                'order' => $i,
                'delay' => $request->get('trigger_delay')[$i] ?? null,
                'message_id' => $request->get('trigger_message_id')[$i] ?? null,
                'campaign_id' => $campaign->id,
                'user_id' => Auth::id(),
            ]
        );
    }

    public function cleanup(array $keepIds, Campaign $campaign)
    {
        foreach ($campaign->schedules()->whereNotIn('id', $keepIds)->get() as $schedule) {
            $schedule->delete();
        }
    }
}
