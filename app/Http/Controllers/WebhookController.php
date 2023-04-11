<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Spatie\Url\Url;
use App\Models\Webhook;
use App\Models\Website;
use App\Models\Event;
use App\Traits\ResponseTrait;

class WebhookController extends Controller
{
    use ResponseTrait;

    public function index(Website $website)
    {
        if (! $website instanceof Website || $website->user_id != Auth::id()) {
            return $this->fail(null, 404);
        }

        return $this->success($website->webhooks()->orderBy('id')->paginate(25));
    }

    public function store(Event $event, Website $website)
    {
        if (empty($website->webhook_url)
            || ! in_array($event->type_id, $website->webhook_event_types ?: [])
        ) {
            return false; // if no webhook URL is set OR type doesn't match, don't use webhooks
        }

        return $event->webhook()->create([
            'website_id' => $event->website->id,
            'event_type_id' => $event->type_id,
            'request_url' => $website->webhook_url,
            'request_method' => $website->webhook_method,
            'status' => config('constants.webhookResponseStatus')['queue'],
        ]);
    }

    public function show(Event $event)
    {
        if (! $event instanceof Event) {
            return $this->fail("Event not found.", 404);
        }

        return $this->success($event->webhook, 200, "events.show");
    }

    public function send(Webhook &$webhook)
    {
        $url = empty($webhook->website->webhook_url) ? null : Url::fromString($webhook->website->webhook_url);

        $webhook->request_url = (string)$url;
        $webhook->request_method = $webhook->website->webhook_method;
        $webhook->request_body = $this->prepBody($webhook);

        if (empty($webhook->website->webhook_url)) {
            throw new \Exception("Website's webhook URL is not set up.");
        }

        if ($webhook->website->webhook_method == config('constants.webhookMethod')['get']) {
            $webhook->request_url = (string)$url->withQueryParameter('payload', json_encode($webhook->request_body));
        }

        $isGet = $webhook->website->webhook_method == config('constants.webhookMethod')['get'];
        $isPost = $webhook->website->webhook_method == config('constants.webhookMethod')['post'];

        if ($isGet) {
            return Http::timeout(10)->get($webhook->request_url);
        } elseif ($isPost) {
            return Http::timeout(10)->asJson()->post($webhook->request_url, $webhook->request_body);
        }

        throw new \Exception("Invalid method {$webhook->request_method}.");
    }

    public function test(Request $request)
    {
        $webhook = new Webhook();
        $webhook->website = $request->website_uuid
            ? Auth::user()->websites()->where('uuid', $request->website_uuid)->first()
            : Website::factory()->make(['user_id' => Auth::id()]);
        $webhook->website->webhook_url = $request->webhook_url;
        $webhook->website->webhook_method = $request->webhook_method;
        $webhook->event = Event::factory()->make(['user_id' => Auth::id()]);

        try {
            $response = $this->send($webhook);

            if ($response->successful()) {
                return response()->json([
                    // 'body' => $response->body(),
                    'success' => true,
                ], 200);
            } else {
                throw new \Exception(
                    "Webhook failed with status " . $response->status() .
                    ($response->body() ? " and body " . $response->body() : null) .
                    "."
                );
            }
        } catch (\Exception $e) {
            return response()->json([
                'body' => $e->getMessage(),
                'success' => false,
            ], 400);
        }
    }

    public function prepBody(Webhook $webhook)
    {
        $webhook->event->website_uuid = $webhook->website->uuid;

        return [
            'event' => $webhook->event->toArray(),
            'subscriber' => $webhook->event->subscriber->toArray(),
            'account_uuid' => $webhook->event->subscriber->user->uuid,
        ];
    }

    public function validateTypes($url, $types): string
    {
        if (empty($url) || empty($types)) {
            return "";
        }

        // The full list of types
        $list = config('constants.eventTypes');

        // When none are posted, assign all
        if (is_string($types)) {
            if (strlen($types) === 0) {
                $types = [];
            } else {
                $types = explode(",", $types);
            }
        }

        // Filter out any bad values
        $types = array_filter($types, "strlen");
        $types = array_intersect($list, $types);

        return join(",", $types);
    }
}
