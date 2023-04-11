<?php

namespace App\Listeners;

// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Events\SubscriberCreated;
use App\Http\Controllers\ScheduleCampaignController;

class ProcessCampaigns
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
    public function handle(SubscriberCreated $event)
    {
        $campaigns = $event->subscriber->website->campaigns()
            ->where('enabled', 1)
            ->where('type', 'waterfall') // run waterfalls first, then the rest (inside the job)
            ->get();

        // if no waterfalls, schedule the rest
        if ($campaigns->count() == 0) {
            $campaigns = $event->subscriber->website->campaigns()
                ->where('enabled', 1)
                ->where('type', '!=', 'waterfall')
                ->get();
        }

        foreach ($campaigns as $campaign) {
            (new ScheduleCampaignController($campaign, $event->subscriber))->get();
        }
    }
}
