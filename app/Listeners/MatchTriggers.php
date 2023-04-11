<?php

namespace App\Listeners;

// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Events\EventCreated;
use App\Http\Controllers\ScheduleTriggerController;
use App\Models\Schedule;
use App\Models\Push;

class MatchTriggers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(EventCreated $event)
    {
        $types = config('constants.eventTypes');

        if ($event->event->type == "notification-clicked" && $event->event->uuid) {
            $push = Push::where('uuid', $event->event->uuid)
                ->with('schedule')
                ->first();

            if (! $push instanceof Push) {
                return;
            }

            if ($push->schedule) {
                Log::warning('Push without a schedule: ' . var_export($push, true));
                return;
            }

            if ($push->schedule->is_trigger) {
                return;
            }

            if ($push->schedule->deleted_at || ! $push->schedule->hasTrigger()) {
                return;
            }

            $schedule = $push->schedule->trigger()->first();

            if (! $schedule instanceof Schedule || $schedule->deleted_at) {
                return;
            }

            (new ScheduleTriggerController($schedule, $event->event));
        }
    }
}
