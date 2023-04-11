<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\ResponseTrait;
use App\Models\Website;
use App\Models\Subscriber;
use App\Models\Campaign;
use App\Models\Schedule;
use App\Models\Message;
use App\Models\Push;
use Inertia\Inertia;

class PushController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        if (request()->wantsJson()) {
            return $this->success(
                Auth::user()->pushes()->orderBy('id', 'asc')->paginate(25),
                200,
                "push.index"
            );
        }

        return Inertia::render('Push/Index', [
            'payload' => Auth::user()->pushes()
                ->with('subscriber')
                ->with('subscriber.website')
                ->with('campaign')
                ->with('message')
                ->with('schedule')
                ->orderBy('id', 'desc')
                ->paginate(25),
            'statusNames' => config('constants.queueResponseStatusNames'),
            'statusColors' => config('constants.queueResponseStatusColor'),
            'statusIcons' => config('constants.queueResponseStatusIcon'),
        ]);
    }

    public function indexByWebsite($websiteUuid = null)
    {
        $website = Auth::user()->websites()->where('uuid', $websiteUuid)->first();

        if (! $website instanceof Website) {
            return $this->fail("Website not found.", 404);
        }

        return $this->success($website->pushes()->orderBy('id')->get());
    }

    public function indexByMessage(int $messageId = 0)
    {
        $message = Auth::user()->messages()->where('id', $messageId)->first();

        if (! $message instanceof Message) {
            return $this->fail("Message not found.", 404);
        }

        return $this->success($message->pushes()->orderBy('id')->get());
    }

    public function indexBySubscriber(Subscriber $subscriber)
    {
        $website = Auth::user()->websites()->where('id', $subscriber->website_id)->first();

        if (! $website instanceof Website) {
            return $this->fail("Subscriber not found.", 404);
        }

        return $this->success($subscriber->pushes()->orderBy('id')->get());
    }

    public function indexBySubscriberAndMessage(Subscriber $subscriber, int $messageId)
    {
        $website = Auth::user()->websites()->where('id', $subscriber->website_id)->first();

        if (! $website instanceof Website) {
            return $this->fail("Subscriber not found.", 404);
        }

        $message = Auth::user()->messages()->where('id', $messageId)->first();

        if (! $message instanceof Message) {
            return $this->fail("Message not found.", 404);
        }

        return $this->success(
            $subscriber->pushes()->where(['message_id' => $message->id])->orderBy('id')->get()
        );
    }

    public function show(int $pushId)
    {
        $push = Auth::user()->pushes()->where('id', '=', $pushId)->first();

        if (! $push instanceof Push) {
            return $this->fail("Push notification not found.", 404);
        }

        return $this->success($push, 200, "push.show");
    }

    public function create(
        Subscriber $subscriber,
        Campaign $campaign,
        Schedule $schedule,
        $scheduled_to_send_at,
        string $response = "Queued",
        int $is_success = 2,
        int $http_code = 0
    ) {
        return Push::create([
            'subscriber_id' => $subscriber->id,
            'campaign_id' => $campaign->id,
            'schedule_id' => $schedule->id,
            'message_id' => $schedule->message->id,
            'uuid' => $schedule->message->s ?? (string)\Uuid::generate(4),
            'scheduled_to_send_at' => $scheduled_to_send_at,
            'response' => $response,
            'is_success' => $is_success,
            'http_code' => $http_code,
            'website_id' => $subscriber->website->id,
            'user_id' => $subscriber->website->user->id,
        ]);
    }

    public function update(
        Push $push,
        Campaign $campaign,
        Schedule $schedule,
        Subscriber $subscriber,
        $response = null,
        $is_success = 0,
        $http_code = 0
    ) {
        return $push->update([
            'subscriber_id' => $subscriber->id,
            'campaign_id' => $campaign->id,
            'schedule_id' => $schedule->id,
            'message_id' => $schedule->message->id,
            'uuid' => $schedule->message->s ?? (string)\Uuid::generate(4),
            'sent_at' => now(),
            'response' => $response,
            'is_success' => $is_success,
            'http_code' => $http_code,
            'website_id' => $subscriber->website->id,
            'user_id' => $subscriber->website->user->id,
        ]);
    }

    public function send(Subscriber $subscriber, Campaign $campaign, Schedule $schedule, Push $push)
    {
        if ($schedule->deleted_at) {
            $this->update($push, $campaign, $schedule, $subscriber, "Schedule not found.", 0, 301);
            throw new \Exception("Schedule not found.", 301);
        }

        if (! $subscriber->subscribed) {
            $this->update($push, $campaign, $schedule, $subscriber, "Subscriber is unsubscribed.", 0, 410);
            throw new \Exception("Subscriber is unsubscribed.", 410);
        }

        if (! $campaign->enabled) {
            $this->update($push, $campaign, $schedule, $subscriber, "Campaign not enabled.", 0, 422);
            throw new \Exception("Campaign not enabled.", 422);
        }

        if ($campaign->deleted_at) {
            $this->update($push, $campaign, $schedule, $subscriber, "Campaign deleted.", 0, 422);
            throw new \Exception("Campaign deleted.", 422);
        }

        if (! $subscriber->website instanceof Website) {
            $this->update($push, $campaign, $schedule, $subscriber, "Website not found.", 0, 404);
            throw new \Exception("Website not found.", 404);
        }

        $auth = [
            'VAPID' => [
                'subject' => 'mailto:status@blastpush.com',
                'publicKey' => $subscriber->website->vapid_public ?? $subscriber->user->vapid_public,
                'privateKey' => $subscriber->website->vapid_private ?? $subscriber->user->vapid_private,
            ],
        ];

        $options = [
            // 'TTL' => 300, // defaults to 4 weeks
            'urgency' => 'normal', // protocol defaults to "normal" ("very-low", "low", "normal", or "high")
            'topic' => $subscriber->website->uuid, // not defined by default (show only the last notification of this topic)
            // 'batchSize' => 200, // defaults to 1000
        ];

        // Log::info(
        //     "Subscriber: " . var_export($subscriber->toArray(), true) . "\n\n" .
        //     "Schedule: " . var_export($schedule->toArray(), true) . "\n\n" .
        //     "Message: " . var_export($schedule->message->toArray(), true)
        // );

        try {
            $webPush = new WebPush($auth, $options);

            $subscription = Subscription::create([
                'endpoint' => $subscriber->endpoint,
                'expirationTime' => $subscriber->expiration,
                'keys' => [
                    "p256dh" => $subscriber->public,
                    "auth" => $subscriber->auth,
                ],
                'contentEncoding' => $subscriber->encoding,
            ]);

            $schedule->message->s = $schedule->message->uuid = $push->uuid ?? (string)\Uuid::generate(4); // Tracking ID

            if ($push->uuid !== $schedule->message->s) {
                $push->uuid = $schedule->message->s;
            }

            $schedule->message->campaignId = $schedule->message->id;
            $schedule->message->icon = $schedule->message->icon ?? $subscriber->website->icon;
            $schedule->message->image = $schedule->message->image ?? $subscriber->website->image;

            $schedule->message->url = (new MessageLinkRenderController(
                $subscriber,
                $campaign,
                $schedule,
                $schedule->message,
                $subscriber->website,
                $schedule->user,
                $push
            ))->get();

            $push->url = $schedule->message->url;

            if ($push->isDirty) {
                $push->save();
            }

            $schedule->message->actions = [[
                // 'id' => 1,
                'text' => $schedule->message->button ?: "View",
                'link' => $schedule->message->url,
                'icon' => $schedule->message->icon
            ]];

            $webPush->sendNotification(
                $subscription,
                json_encode($schedule->message)
            );

            foreach ($webPush->flush() as $report) {
                $response = $report->getResponse();

                if (! $this->update(
                    $push,
                    $campaign,
                    $schedule,
                    $subscriber,
                    $report->getReason(),
                    $report->isSuccess() ? 1 : 0,
                    is_object($response) ? $response->getStatusCode() : 0
                )) {
                    throw new \Exception("Unable to update push.");
                }
            }

            return $report->isSuccess();

        } catch (\Exception $e) {
            $this->update(
                $push,
                $campaign,
                $schedule,
                $subscriber,
                $e->getMessage(),
                0
            );

            throw $e;
        }
    }

    public function push(Subscriber $subscriber, Message $message)
    {
        $auth = [
            'VAPID' => [
                'subject' => 'mailto:status@blastpush.com',
                'publicKey' => $subscriber->website->vapid_public ?? $subscriber->user->vapid_public,
                'privateKey' => $subscriber->website->vapid_private ?? $subscriber->user->vapid_private,
            ],
        ];

        $options = [
            // 'TTL' => 300, // defaults to 4 weeks
            'urgency' => 'normal', // protocol defaults to "normal" ("very-low", "low", "normal", or "high")
            'topic' => $subscriber->website->uuid, // not defined by default (show only the last notification of this topic)
            // 'batchSize' => 200, // defaults to 1000
        ];

        $httpcode = 0;
        $reason = null;
        $success = 0;

        try {
            $webPush = new WebPush($auth, $options);

            $subscription = Subscription::create([
                'endpoint' => $subscriber->endpoint,
                'expirationTime' => $subscriber->expiration,
                'keys' => [
                    "p256dh" => $subscriber->public,
                    "auth" => $subscriber->auth,
                ],
                'contentEncoding' => $subscriber->encoding,
            ]);

            $message->campaignId = $message->id;
            $message->icon = $message->icon ?? $subscriber->website->icon;
            $message->image = $message->image ?? $subscriber->website->image;

            $message->actions = [[
                // 'id' => 1,
                'text' => $message->button ?: "View",
                'link' => $message->url,
                'icon' => $message->icon
            ]];

            $webPush->sendNotification(
                $subscription,
                json_encode($message)
            );

            foreach ($webPush->flush() as $report) {
                if (is_object($report)) {
                    $response = $report->getResponse();
                    $httpcode = is_object($response) ? $response->getStatusCode() : 0;
                    $reason = $report->getReason();
                    $success = $report->isSuccess() ? 1 : 0;
                } else {
                    throw new \Exception('Report object not found', 404);
                }
            }

            return [$httpcode, $reason, $success];
        } catch (\Exception $e) {
            $httpcode = $e->getCode() ?? 0;

            return [$httpcode, $e->getMessage(), $success];
        }
    }
}
