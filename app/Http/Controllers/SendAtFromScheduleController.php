<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\Subscriber;
use App\Models\Schedule;
use App\Models\Push;

class SendAtFromScheduleController extends Controller
{
    protected $sendAt;

    public function __construct($type, Schedule $schedule)
    {
        $sendAt = null;

        switch ($type) {
            case 'waterfall':
            case 'trigger':
                $sendAt = now()->addMinutes($schedule->delay);
                break;

            case 'scheduled':
                $sendAt = $schedule->scheduled_at;
                break;

            case 'reoccurring':
                $sendAt = now();
                $sendAt->second = 0;

                switch ($schedule->reoccurring_frequency) {
                    case "hourly":
                        $sendAt->minute = date("i", strtotime($schedule->hour_minute));
                        if ($sendAt->isPast()) {
                            $sendAt->addHour();
                        }
                        break;

                    case "daily":
                        $sendAt->setTimeFrom($schedule->hour_minute);
                        if ($sendAt->isPast()) {
                            $sendAt->addDay();
                        }
                        break;

                    case "weekly":
                        $weekday = config('constants.campaignWeekdays')[$schedule->day];
                        $sendAt->next($weekday);
                        $sendAt->setTimeFrom($schedule->hour_minute);
                        break;

                    case "monthly":
                        $sendAt->day = $schedule->day;
                        $sendAt->addMonth();
                        $sendAt->setTimeFrom($schedule->hour_minute);
                        break;
                }
                break;
        }

        $this->sendAt = $sendAt;
    }

    public function get()
    {
        return $this->sendAt;
    }
}
