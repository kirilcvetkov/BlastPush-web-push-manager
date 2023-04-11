<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Redirect;
use Inertia\Inertia;
use App\Traits\ResponseTrait;
use App\Models\Website;
use App\Models\Dialog;
use App\Models\Campaign;

class WebsiteController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        if (request()->wantsJson()) {
            $payload = Auth::user()->websites();
        } else {
            $payload = Auth::user()->websites()->withCount(['campaigns', 'subs', 'unsubs', 'pushes']);
        }

        return Inertia::render('Website/Index', [
            'websites' => $payload->orderBy('id')->paginate(25),
            'dialogs' => (new DialogController())->index()->toArray(),
        ]);
    }

    public function create()
    {
        return $this->edit();
    }

    public function store(Request $request)
    {
        return $this->update($request);
    }

    public function show(Website $website)
    {
        // return $this->success($website);
    }

    public function edit(string $uuid = null)
    {
        $website = null;

        if ($uuid) {
            $website = Website::where('uuid', $uuid)->first();
        }

        if (! $website instanceof Website) {
            $website = new Website();
            $website->webhook_event_types = [];
        }

        if (! $website->campaigns instanceof Campaign) {
            $website->campaigns = new Campaign();
        }

        $website->dedupe_subscribers = (bool)$website->dedupe_subscribers;

        $campaignsList = Auth::user()->campaigns()->get();
        $types = config('constants.eventTypesDetails');

        return Inertia::render('Website/Create', $website->toArray() + compact('campaignsList', 'types'));
    }

    public function update(Request $request, $websiteUuid = null)
    {
        $request->merge((new ImageUploadController($request, $websiteUuid, 'website'))->get());

        $validation = Validator::make(
            array_filter($request->json()->all() ?: $request->all()),
            [
                "name" => [
                    'required', 'unique:websites,name,' . $websiteUuid . ',uuid,deleted_at,NULL,user_id,' . Auth::id()
                ],
                "domain" => ['required', 'string'],
                "dedupe_subscribers" => ['boolean'],
                "dialog_id" => ['integer'],
                "webhook_url" => ['url'],
                "webhook_method" => ['integer'],
                "webhook_event_types" => ['array'],
                "webhook_event_types.*" => [Rule::in(array_keys(config('constants.eventTypes')))],
            ]
        );

        if ($validation->fails()) {
            return $this->fail($validation->errors(), 422);
        }

        $webhookMethodGet = config("constants.webhookMethod")['get'];

        $data = [
            "name" => $request->json("name") ?: $request->name,
            "domain" => $request->json("domain") ?: $request->domain,
            "icon" => $request->json("icon") ?: $request->icon,
            "image" => $request->json("image") ?: $request->image,
            "dedupe_subscribers" => $request->json("dedupe_subscribers") ?: $request->dedupe_subscribers ?: false,
            "webhook_url" => $request->json("webhook_url") ?: $request->webhook_url,
            "webhook_method" => $request->json("webhook_method") ?: $request->webhook_method ?: $webhookMethodGet,
            "webhook_event_types" => $request->json("webhook_event_types") ?: $request->webhook_event_types ?: null,
            "dialog_id" => $this->dialogId($request),
        ];

        if (is_null($websiteUuid)) {
            $website = new Website($data);
            $website = Auth::user()->websites()->save($website);
        } else {
            $website = Auth::user()->websites()->where('uuid', $websiteUuid)->first();

            if (! $website instanceof Website) {
                return $this->fail("Website not found.", 404);
            }

            if ($this->exists($websiteUuid, $request->json('name'))) {
                return $this->fail(null, 409);
            }

            $website->fill($data);
            $website->save();
            $website->refresh();
        }

        $website->campaigns()->sync($this->validateCampaigns($request->get('campaigns')));

        return Redirect::route('websites.index')->with('success', "Success.");
        // return $this->success(
        //     $website,
        //     (is_null($websiteUuid) ? 201 : 200),
        //     null,
        //     "Website {$website->name} " . (is_null($websiteUuid) ? "created." : "updated."),
        //     "websites.index"
        // );
    }

    public function validateCampaigns($campaigns)
    {
        if (empty($campaigns)) {
            return [];
        }

        return array_intersect(array_column($campaigns, 'id'), Auth::user()->campaigns->pluck('id')->toArray());
    }

    public function dialogId(Request $request)
    {
        $dialogId = $request->json("dialog_id") ?: $request->dialog_id;

        if (! $dialogId) {
            $dialogId = Auth::user()->dialogs()->where("is_global", '=', 1)->first()->id;
        }

        return $dialogId;
    }

    public function exists($websiteUuid, $name)
    {
        $exists = Auth::user()->websites()
            ->where('name', '=', $name);

        if (strlen($websiteUuid) > 0) {
            $exists->where('uuid', '!=', $websiteUuid);
        }

        return $exists->first() instanceof Website;
    }

    public function destroy($websiteUuid)
    {
        $website = Auth::user()->websites()->where('uuid', $websiteUuid)->first();

        if (! $website instanceof Website) {
            return Redirect::back()->with('error', "Website {$website->name} not found.");
            return $this->fail("Website not found.", 404);
        }

        $website->delete();
        $website->refresh();

        return Redirect::back()->with('success', "Website {$website->name} deleted.");
    }
}
