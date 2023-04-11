<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\Models\Subscriber;
use App\Models\Schedule;
use App\Models\Push;

class IsScheduleSentAlreadyController extends Controller
{
    protected $push;
    protected $safeMinutes = [
        "hourly" => 55,
        "daily" => 23 * 60,
        "weekly" => (7 * 24 * 60) - 60, // 6 days, 23 hours
        "monthly" => (28 * 24 * 60) - 60, // 27 days, 23 hours
    ];

    public function __construct($type, Subscriber $subscriber, Schedule $schedule)
    {
        switch ($type) {
            case 'waterfall':
            case 'scheduled':
                $push = $subscriber->pushes()
                    ->where('schedule_id', $schedule->id)
                    ->first();
                break;

            case 'reoccurring':
                $push = $subscriber->pushes()
                    ->where('schedule_id', $schedule->id)
                    ->orderBy('id', 'desc')
                    ->first();

                if (! $push instanceof Push) {
                    break; // not sent
                }

                $sent = $push->sent_at ?? $push->scheduled_to_send_at ?? $push->updated_at ?? $push->created_at;

                if (! $sent instanceof Carbon) {
                    $push = null; // not sent
                    break;
                }

                switch ($schedule->reoccurring_frequency) {
                    case "hourly":
                        if ($sent->diffInMinutes() < $this->safeMinutes["hourly"]) {
                            $push = null; // not sent
                        }
                        break;

                    case "daily":
                        if ($sent->diffInMinutes() < $this->safeMinutes["daily"]) {
                            $push = null; // not sent
                        }
                        break;

                    case "weekly":
                        if ($sent->diffInMinutes() < $this->safeMinutes["weekly"]) {
                            $push = null; // not sent
                        }
                        break;

                    case "monthly":
                        if ($sent->diffInMinutes() < $this->safeMinutes["monthly"]) {
                            $push = null; // not sent
                        }
                        break;
                }

                break;
        }

        $this->push = $push;
    }

    public function get()
    {
        return $this->push;
    }
}
