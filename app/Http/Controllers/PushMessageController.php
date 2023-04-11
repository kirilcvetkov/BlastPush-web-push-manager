<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseTrait;
use App\Models\Website;
use App\Models\Subscriber;
use App\Models\Campaign;
use App\Models\Schedule;
use App\Models\Message;
use App\Models\Push;

class PushMessageController extends Controller
{
    use ResponseTrait;

    public function send(int $subscriber_id, int $message_id)
    {
        $subscriber = Subscriber::withTrashed()->where('id', $subscriber_id)->first();

        if (! $subscriber instanceof Subscriber) {
            return $this->fail("Subscriber not found.", 404);
        }

        // Check if the user owns the subscriber first
        $website = Auth::user()->websites()->where('id', $subscriber->website_id)->first();

        if (! $website instanceof Website) {
            return $this->fail("Subscriber not found.", 404);
        }

        if ($subscriber->trashed()) {
            return $this->fail("Subscriber is deleted.", 404);
        }

        $message = Auth::user()->messages()->where('id', $message_id)->first();

        if (! $message instanceof Message) {
            return $this->fail("Message not found.", 404);
        }

        $message->s = (string)\Uuid::generate(4);
        $message->campaignId = $message->id;

        $message->url = (new MessageLinkRenderController(
            $subscriber,
            new Campaign(),
            new Schedule(),
            $message,
            $website,
            Auth::user(),
            (new Push())->fill(['uuid' => $message->s])
        ))->get();

        [$httpcode, $reason, $success] = (new PushController())->push($subscriber, $message);

        $push = $subscriber->pushes()->create([
            'subscriber_id' => $subscriber->id,
            'campaign_id' => null,
            'schedule_id' => null,
            'message_id' => $message->id,
            'uuid' => $message->s,
            'url' => $message->url,
            'scheduled_to_send_at' => null,
            'response' => $reason,
            'is_success' => $success,
            'http_code' => $httpcode,
            'website_id' => $website->id,
            'user_id' => Auth::id(),
        ]);

        if ($success) {
            return $this->success($push, $push->http_code);
        }

        return $this->fail($reason);
    }
}
