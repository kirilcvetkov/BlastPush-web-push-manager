<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Campaign;
use App\Models\Schedule;
use Inertia\Inertia;

class CampaignController extends Controller
{
    use \App\Traits\ResponseTrait;

    public function index()
    {
        $payload = [];

        foreach (config('constants.campaignTypes') as $type) {
            $payload[$type] = Auth::user()
                ->campaigns()
                ->where('type', $type)
                ->with('schedules')
                ->withCount(['websites', 'schedules', 'triggers'])
                ->orderBy('id')
                ->paginate(25);
        }

        if (request()->wantsJson()) {
            return $this->success(
                $payload,
                200,
                "campaigns.index"
            );
        }

        return Inertia::render('Campaign/Index', $payload);
    }

    public function create()
    {
        return $this->edit(new Campaign());
    }

    public function validation($campaignId = "NULL")
    {
        return [
            "name" => [
                'required',
                'unique:campaigns,name,' . $campaignId . ',id,deleted_at,NULL,user_id,' . Auth::id()
            ],
            "enabled" => ['required', 'boolean'],
            "websites" => ['required', 'array'],
            "schedules" => ['required', 'array'],
            "type" => [Rule::in(config('constants.campaignTypes'))],
        ];
    }

    public function store(Request $request)
    {
        return $this->update($request, new Campaign());
    }

    public function edit(Campaign $campaign)
    {
        $associated = $campaign->websites->pluck("id")->toArray();
        $schedules = $campaign->schedules->where('is_trigger', 0)->sortBy('order');
        $messages = Auth::user()->messages()->get();
        $websitesList = Auth::user()->websites()->get();

        return Inertia::render('Campaign/Create',
            $campaign->toArray() + compact('associated', 'messages', 'websitesList', 'schedules')
        );
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validation = Validator::make($request->all(), $this->validation($campaign->id));

        if ($validation->fails()) {
            return $this->fail($validation->errors(), 422);
        }

        $new = is_null($campaign->id);

        $campaign->fill($request->all());

        if ($new) {
            $campaign->user_id = Auth::id();
        }

        $campaign->save();
        $campaign->refresh();

        $campaign->websites()->sync(
            (new WebsiteValidationController())->get($request->get('websites'))
        );

        (new ScheduleController())->update($request, $campaign);

        return $this->success(
            $campaign,
            $new ? 201 : 200,
            null,
            "Campagin {$campaign->name} " . ($new ? "created." : "updated."),
            "campaigns.index",
            "type=" . $campaign->type
        );
    }

    public function destroy(int $campaignId)
    {
        $campaign = Auth::user()->campaigns()->where('id', $campaignId)->first();

        if (! $campaign instanceof Campaign) {
            return $this->fail("Campaign not found.", 404);
        }

        $campaign->schedules()->delete();
        $campaign->delete();
        $campaign->refresh();

        return $this->success(
            $campaign,
            200,
            null,
            "Website {$campaign->name} deleted.",
            "campaigns.index",
            "type=" . $campaign->type
        );
    }
}
