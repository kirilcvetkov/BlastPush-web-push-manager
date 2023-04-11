<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponseTrait;
use App\Models\Subscriber;
use App\Models\Website;
use App\Models\Message;
use App\Models\Campaign;
use App\Models\Schedule;
use App\Models\Push;

class PushManualController extends Controller
{
    use ResponseTrait;

    public function send(int $subscriber_id, Request $request)
    {
        $subscriber = Subscriber::withTrashed()->where('id', $subscriber_id)->first();

        if (! $subscriber instanceof Subscriber) {
            return $this->fail("Subscriber not found.", 404);
        }

        if ($subscriber->trashed()) {
            return $this->fail("Subscriber is deleted.", 404);
        }

        // Check if the user owns the subscriber first
        $website = Auth::user()->websites()->where('id', $subscriber->website_id)->first();

        if (! $website instanceof Website) {
            return $this->fail("Subscriber not found.", 404);
        }

        $data = $request->json()->all() ?: $request->all();

        $validation = Validator::make(
            $data,
            (new MessageController())->validation()
        );

        if ($validation->fails()) {
            return $this->fail($validation->errors(), 422);
        }

        $message = (new Message())->fill($data);

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
            'message_id' => null,
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

        if ($httpcode == 410) {
            $subscriber->subscribed = 0;
            $subscriber->save();
            $subscriber->delete();
            $subscriber->refresh();

            return $this->fail($subscriber, $push->http_code);
        }

        return $this->fail($reason, $push->http_code);
    }
}
