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
use App\Models\Event;

class ScheduleTriggerController extends Controller
{
    protected $schedule;

    public function __construct(Schedule $schedule, Event $event)
    {
        $this->schedule = $schedule;
        $campaign = $schedule->campaign;

        if (! $campaign instanceof Campaign) {
            throw new \Exception("Campaign not found", 422);
        }

        // Is sent already?
        $push = (new IsScheduleSentAlreadyController($campaign->type, $event->subscriber, $this->schedule))->get();

        if ($push instanceof Push) {
            return;
        }

        $sendAt = (new SendAtFromScheduleController('trigger', $this->schedule))->get(); // $campaign->type

        if (is_null($sendAt)) {
            Log::channel('job')
                ->error("Unable to find a send date for type {$campaign->type} + schedule {$this->schedule->id}");
            return;
        }

        $push = (new PushController())
            ->create($event->subscriber, $campaign, $this->schedule, $sendAt);

        Log::channel('job')->debug(
            "Queue | Sub {$event->subscriber->id} | Camp {$campaign->id} | Schedule {$this->schedule->id} | " .
            "Push {$push->id} | SendAt {$sendAt} - delay " . $sendAt->diffInMinutes() . " | " . $sendAt->diffInSeconds()
        );

        dispatch(new SendPushNotification($event->subscriber, $campaign, $this->schedule, $push))
            ->delay($sendAt->diffInSeconds());
    }

    public function get()
    {
        return $this->schedule;
    }
}
