<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PushController;
use App\Http\Controllers\ScheduleCampaignController;
use App\Models\Subscriber;
use App\Models\Campaign;
use App\Models\Schedule;
use App\Models\Push;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subscriber;
    public $campaign;
    public $schedule;
    public $push;
    public $tries = 2;
    public $success = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Subscriber $subscriber, Campaign $campaign, Schedule $schedule, Push $push)
    {
        $this->subscriber = $subscriber;
        $this->campaign = $campaign;
        $this->schedule = $schedule;
        $this->push = $push;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        try {
            $this->success = (new PushController())
                ->send($this->subscriber, $this->campaign, $this->schedule, $this->push);
            Log::channel('job')->debug("Send success: " . var_export($this->success, true));
        } catch (Exception $e) {
            Log::channel('job')->debug(
                "Send failed: " . $e->getCode() . " | " . $e->getMessage() . " | " . $e->getLine() . " | " . $e->getFile()
            );
        }

        if ($this->success === false) {
            // Unsubscribed / expired subscription / 403 Forbidden
            if ($this->push->http_code == 410 || $this->push->http_code == 403) {
                $this->subscriber->subscribed = 0;
                $this->subscriber->save();
                $this->subscriber->delete();
                $this->subscriber->refresh();
                return;
            }

            // 422 :: Campaign not enabled / Campaign deleted
            if ($this->push->http_code == 422 && $this->campaign->type == 'reoccurring') {
                return;
            }
        }

        // No follow up schedules after triggers
        if ($this->schedule->is_trigger) {
            return;
        }

        $schedule = (new ScheduleCampaignController($this->campaign, $this->subscriber))->get();

        // When the waterfall runs out of schedules, process the rest of the campaigns
        if ($this->campaign->type == 'waterfall' && ! $schedule instanceof Schedule) {
            $campaigns = $this->subscriber->website->campaigns()
                ->where('enabled', 1)
                ->where('type', '!=', 'waterfall')
                ->get();

            foreach ($campaigns as $campaign) {
                (new ScheduleCampaignController($campaign, $this->subscriber))->get();
            }
        }
    }
}
