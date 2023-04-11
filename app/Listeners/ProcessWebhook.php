<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Events\WebhookCreated;

class ProcessWebhook
{
    use Dispatchable, InteractsWithQueue; //, SerializesModels;

    // public $connection = 'sqs';
    public $tries = 3;
    public $delays = [
        1 => 120,
        2 => 600,
    ];

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
    public function handle(WebhookCreated $created)
    {
        $webhook = $created->webhook;

        $success = config('constants.webhookResponseStatus')['success'];
        $fail = config('constants.webhookResponseStatus')['fail'];
        $queueRetry = config('constants.webhookResponseStatus')['queue-retry'];

        try {
            $response = app('App\Http\Controllers\WebhookController')->send($webhook);

            if ($response->successful()) {
                $webhook->status = $success;
            } else {
                $webhook->status = $this->attempts() < $this->tries ? $queueRetry : $fail;
            }

            $webhook->tries = $this->attempts();
            $webhook->response_body = $response->body();
            $webhook->response_status = $response->status();
            $webhook->response_headers = json_encode($response->headers());

            $webhook->save();

            if (! $response->successful() && $this->attempts() < $this->tries) {
                throw new \Exception("Webhook failed with status " . $webhook->response_status . ".");
            }
        } catch (\Exception $e) {
            $webhook->tries = $this->attempts();
            $webhook->response_body = $e->getMessage();
            $webhook->status = $this->attempts() < $this->tries ? $queueRetry : $fail;

            $webhook->save();

            Log::channel('job')->debug(
                $webhook->id . " | Tries " . $this->attempts() . " < {$this->tries} | " . $e->getMessage()
            );

            if ($this->attempts() < $this->tries) {
                $secondsDelayBeforeRetry = app()->isLocal() ? 1 : $this->delays[$this->attempts()];
                $this->release($secondsDelayBeforeRetry);
            } else {
                throw $e;
            }
        }
    }
}
