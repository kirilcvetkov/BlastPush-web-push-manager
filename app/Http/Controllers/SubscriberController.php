<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Traits\ResponseTrait;
use App\Models\Subscriber;
use App\Models\Website;
use App\Models\User;
use Inertia\Inertia;

class SubscriberController extends Controller
{
    use ResponseTrait;

    public function index($websiteUuid = null)
    {
        $sort = request()->wantsJson() ? "asc" : "desc";

        if (is_null($websiteUuid)) {
            if (request()->wantsJson()) {
                $subscribers = Auth::user()->subscribers()
                    ->with('website')
                    ->orderBy('id', $sort)
                    ->paginate(25);
            } else {
                return Inertia::render('Subscribers/Index', [
                    'payload' => Auth::user()->subscribers()
                        ->with('website')
                        ->with('event')
                        ->withCount(['pushes', 'events'])
                        ->orderBy('id', $sort)
                        ->paginate(25),
                ]);
            }

            return $this->success($subscribers, 200, "subscribers.index");
        }

        $website = Auth::user()->websites()->where('uuid', $websiteUuid)->first();

        if ($website instanceof Website) {
            return $this->success(
                $website->subscribers()->orderBy('id', $sort)->paginate(25),
                200,
                "subscribers.index"
            );
        }

        return $this->fail('Website not found.', 404);
    }

    public function hash(Request $request, $generate = false)
    {
        if (is_null($request->json('hash')) || $request->json('hash') === 'undefined' || $generate) {
            return md5($request->json('subscription.endpoint'));
        }

        return $request->json('hash');
    }

    public function store(Request $request, Website $website, bool $internal = false)
    {
        if (! $website instanceof Website) {
            return $this->fail('Website not found.', 404);
        }

        $fields = [
            'endpoint' => $request->json('subscription.endpoint'),
            'hash' => $this->hash($request),
            'public' => $request->json('subscription.keys.p256dh'),
            'auth' => $request->json('subscription.keys.auth'),
            'expiration' => $request->json('subscription.expirationTime'),
            'encoding' => $request->json('encoding'),
            'body' => json_encode($request->json()->all()),
            'variables' => $request->json('variables'),
            'subscribed' => 1,
            'deleted_at' => null,
        ];

        if (strtotime($request->json('created_at'))) {
            $fields['created_at'] = $request->json('created_at');
        }

        // Log::debug("Fields: " . var_export($fields, true));

        $validation = Validator::make($fields, [
            'endpoint' => 'required',
            'public' => 'required',
            'auth' => 'required',
        ]);

        if ($validation->fails()) {
            Log::error($validation->errors());
            return $this->fail($validation->errors(), 400);
        }

        $fields['variables'] = (new VariableFilterController($fields['variables'], $website->user_id))->get();
        $fields['website_id'] = $website->id;
        $fields['user_id'] = $website->user_id;

        $subscriber = $this->find($website, $fields['hash']);

        if (! $subscriber instanceof Subscriber && $fields['hash'] != $this->hash($request, true)) {
            $subscriber = $this->find($website, $this->hash($request, true));
        }

        if (! $subscriber instanceof Subscriber) {
            $subscriber = $website->subscribers()->create($fields);
        } else {
            $fields['variables'] = (new VariableMergeController($subscriber, $fields['variables']))->get();
            $subscriber->update($fields);
        }

        if ($internal) {
            return $subscriber;
        } elseif ($subscriber->wasRecentlyCreated) {
            $request->merge(['type' => 'subscribe']);
            $event = app('App\Http\Controllers\EventController')
                ->store($request, $website, $subscriber);
        }

        return $this->success(
            $subscriber,
            $subscriber->wasRecentlyCreated ? 201 : 200
        );
    }

    public function find(Website $website, $hash)
    {
        $subscriber = Subscriber::where('subscribers.user_id', $website->user_id)
            ->where('hash', $hash)
            ->when($website->dedupe_subscribers,
                function ($query) use ($website) {
                    return $query->leftJoin('websites', 'websites.id', '=', 'subscribers.website_id')
                        ->where('websites.dedupe_subscribers', $website->dedupe_subscribers)
                        ->where('websites.user_id', $website->user_id);
                },
                function ($query) use ($website) {
                    return $query->where('website_id', $website->id);
                })
            ->withTrashed()
            ->first();

        return $subscriber;
    }

    public function show(int $subscriber_id)
    {
        $subscriber = Subscriber::where('id', $subscriber_id)->withTrashed()->first();

        if ($subscriber instanceof Subscriber) {
            return $subscriber;
        }

        return response()->json(null, 404);
    }

    public function destroy(Request $request, Website $website, bool $internal = false)
    {
        $subscriber = $website->subscribers()->where([
            'website_id' => $website->id,
            'hash' => $this->hash($request),
        ])->first();

        if (! $subscriber instanceof Subscriber) {
            return $internal ? null : $this->fail(null, 404);
        } else {
            $json = $request->json()->all();
            $json['type'] = 'unsubscribe';
            $request->json()->replace($json);
            app('App\Http\Controllers\EventController')->store($request, $website, $subscriber);
        }

        $subscriber->subscribed = 0;
        $subscriber->save();
        $subscriber->delete();
        $subscriber->refresh();

        return $internal
            ? $subscriber
            : $this->success($subscriber, 200, null, "Subscriber unsubscribed.", "subscribers.index");
    }
}
