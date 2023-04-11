<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Jobs\SendPushNotification;
use App\Http\Controllers\PushController;
use App\Http\Controllers\SendAtFromScheduleController;
use App\Http\Controllers\IsScheduleSentAlreadyController;
use App\Models\Subscriber;
use App\Models\Campaign;
use App\Models\Schedule;
use App\Models\Push;

class ScheduleCampaignController extends Controller
{
    protected $schedule;

    public function __construct(Campaign $campaign, Subscriber $subscriber)
    {
        switch ($campaign->type) {
            case "waterfall":
                $schedules = $campaign->schedules()
                    ->whereNull('is_trigger')
                    ->orderBy('order')
                    ->get();

                if ($schedules->count() == 0) {
                    return;
                }

                foreach ($schedules as $schedule) {
                    $push = (new IsScheduleSentAlreadyController($campaign->type, $subscriber, $schedule))->get();

                    if (! $push instanceof Push) {
                        $this->schedule = $schedule;
                        break;
                    }
                }

                if (! $this->schedule instanceof Schedule) {
                    return;
                }
                break;

        case "scheduled":
        case "reoccurring":
            $this->schedule = $campaign->schedules()
                ->whereNull('is_trigger')
                ->first(); // there's only one schedule for scheduled/reoccurring

            if (! $this->schedule instanceof Schedule) {
                return;
            }

            // Is Expired?
            if ($campaign->type == "scheduled" && $this->schedule->scheduled_at->isPast()) {
                return;
            }

            // Is sent already?
            $push = (new IsScheduleSentAlreadyController($campaign->type, $subscriber, $this->schedule))->get();

            if ($push instanceof Push) {
                return;
            }
            break;
        }

        $sendAt = (new SendAtFromScheduleController($campaign->type, $this->schedule))->get();

        if (is_null($sendAt)) {
            Log::channel('job')
                ->error("Unable to find a send date for type {$campaign->type} + schedule {$this->schedule->id}");
            return;
        }

        $push = (new PushController())
            ->create($subscriber, $campaign, $this->schedule, $sendAt);

        Log::channel('job')->debug(
            "Queue | Sub {$subscriber->id} | Camp {$campaign->id} | Schedule {$this->schedule->id} | " .
            "Push {$push->id} | SendAt {$sendAt} - delay " . $sendAt->diffInMinutes() . " | " . $sendAt->diffInSeconds()
        );

        dispatch(new SendPushNotification($subscriber, $campaign, $this->schedule, $push))
            ->delay($sendAt->diffInSeconds());
    }

    public function get()
    {
        return $this->schedule;
    }
}
