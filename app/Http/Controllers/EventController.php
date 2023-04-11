<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Website;
use App\Models\Push;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseTrait;
use Inertia\Inertia;

class EventController extends Controller
{
    use ResponseTrait;

    public function index($websiteUuid = null)
    {
        $sort = request()->wantsJson() ? "asc" : "desc";

        if (empty($websiteUuid)) {
            return Inertia::render('Events/Index', [
                'events' => Auth::user()->events()
                    ->with('webhook')
                    ->with('website')
                    ->with('subscriber')
                    ->orderBy('id', $sort)
                    ->paginate(25),
                'eventTypesDetails' => config("constants.eventTypesDetails"),
                'webhookStatusColors' => config('constants.webhookResponseStatusColor'),
                'webhookStatusIcons' => config('constants.webhookResponseStatusIcon')
            ]);
        }

        $website = Auth::user()->websites()->where('uuid', $websiteUuid)->first();

        if (! $website instanceof Website) {
            return $this->fail("Website not found.", 404);
        }

        return $this->success($website->events()->orderBy('id', $sort)->paginate(25));
    }

    public function store(Request $request, Website $website, $subscriber = null, $internal = false)
    {
        $types = config('constants.eventTypesArr');
        $typeId = $types[$request->json('type', key($types))];

        if (! $subscriber instanceof Subscriber) {
            $subscriber = null;

            if ($request->json('subscription.endpoint') || $request->json('hash')) {
                if ($typeId == $types["unsubscribe"]) {
                    $subscriber = app('App\Http\Controllers\SubscriberController')
                        ->destroy($request, $website, true);
                } else {
                    $subscriber = app('App\Http\Controllers\SubscriberController')
                        ->store($request, $website, true);
                }
            } elseif ($request->json('uuid')) {
                $push = Push::where('uuid', $request->json('uuid'))->first();

                if ($push instanceof Push) {
                    $subscriber = $push->subscriber;
                }
            }

            if (! $subscriber instanceof Subscriber) {
                return $this->fail("Subscriber not found.", 404);
            }

            if ($website->id !== $subscriber->website_id) {
                return $this->fail("Website is incorrect.", 400);
            }
        }

        $ua = $request->json('user_agent', $request->userAgent());
        $browser = browser($ua);

        $ip = $request->json('ip', $request->ip());
        $geo = geoIp($ip);

        $fields = [
            'type_id' => $typeId,
            'type' => $request->json('type', 'unknown'),
            'url' => $request->json('url'),
            'location' => urldecode($request->json('location')),
            'ip' => $ip,
            'country' => $request->json('country', $geo['country']),
            'state' => $request->json('state', $geo['state']),
            'city' => $request->json('city', $geo['city']),
            'postal' => $request->json('postal', $geo['postal']),
            'timezone' => $request->json('timezone', $geo['timezone']),
            'user_agent' => $ua,
            'device' => $browser->device(),
            'device_version' => $browser->deviceVersion(),
            'platform' => $browser->platform(),
            'platform_version' => $browser->platformVersion(),
            'browser' => $browser->browser(),
            'browser_version' => $browser->browserVersion(),
            'browser_version_short' => $browser->browserVersionShort(),
            'is_mobile' => $browser->isMobile(),
            'is_tablet' => $browser->isTablet(),
            'is_desktop' => $browser->isDesktop(),
            'is_robot' => $browser->isRobot(),
            'referer' => $request->header('Referer', $request->server('HTTP_REFERER')),
            'body' => json_encode($request->json()->all()),
            'uuid' => $request->json('uuid'),
            'website_id' => $website->id,
            'user_id' => $website->user_id,
        ];

        $validation = Validator::make($fields, [
            'type_id' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->fail($validation->errors(), 400);
        }

        $event = $subscriber->events()->create($fields);

        if ($event && $website->webhook_url) {
            app('App\Http\Controllers\WebhookController')
                ->store($event, $website);
        }

        if ($internal) {
            return $event;
        }

        return $this->success(
            $event,
            $subscriber->wasRecentlyCreated ? 201 : 200
        );
    }

    public function show(int $event_id)
    {
        $event = Auth::user()->events()->where('id', $event_id)->first();

        if (! $event instanceof Event) {
            return $this->fail("Event not found.", 404);
        }

        return $this->success($event);
    }
}
