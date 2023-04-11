<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use MatthiasMullie\Minify;
use App\Models\Dialog;
use App\Models\Website;
use App\Traits\ResponseTrait;
use Inertia\Inertia;

class DialogController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        if (request()->wantsJson()) {
            $payload = Auth::user()->dialogs();
        } else {
            return Auth::user()->dialogs()->withCount('websites')->orderBy('id')->paginate(25);
        }

        return $this->success(
            $payload->orderBy('id')->paginate(25),
            200,
            "dialogs.index"
        );
    }

    public function create()
    {
        return $this->edit(new Dialog());
    }

    public function edit(Dialog $dialog)
    {
        $websitesList = Auth::user()->websites()->get();

        $dialog->websites = $dialog->websites->all();

        if ($dialog->image == $dialog->defaultImage) {
            $dialog->image = null;
        }

        return Inertia::render(
            'Website/Dialog',
            $dialog->toArray() + compact('websitesList')
        );
        // return view('dialogs.edit', compact('dialog', 'websites'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            "message" => ['max:500', 'required'],
            "image" => ['url', 'max:500', 'nullable'],
            "delay" => ['integer'],
            "button_allow" => ['max:50'],
            "button_block" => ['max:50'],
            "websites" => ['array'],
            "show_percentage" => ['integer'],
        ]);
    }

    public function store(Request $request)
    {
        return $this->update($request);
    }

    public function update(Request $request, int $dialogId = 0)
    {
        $request->merge((new ImageUploadController($request, $dialogId, 'dialog'))->get());

        $validation = $this->validator($request->all());

        if ($validation->fails()) {
            return $this->fail($validation->errors(), 422);
        }

        $dialog = Auth::user()->dialogs()->where('id', $dialogId)->first();

        if (! $dialog instanceof Dialog) {
            return $this->fail("Dialog not found.", 404);
        }

        $dialog->fill($request->json()->all() ?: $request->all());

        $dialog->save();
        $dialog->refresh();

        $current = $dialog->websites()->get();

        foreach ((new WebsiteValidationController())->get($request->get('websites')) as $website) {
            $website->dialog_id = $dialog->id;
            $website->save();
            $current = $current->where("uuid", "!=", $website->uuid);
        }

        if ($current->isNotEmpty()) {
            $global = Auth::user()->dialogs()->where("is_global", 1)->first();

            foreach ($current as $website) {
                $website->dialog_id = $global->id;
                $website->save();
            }
        }

        return \Redirect::route('websites.index')->with('success', "Success.")->with('tab', 'dialog');
        // return $this->success($dialog, 200, null, "Dialog updated.", "dialogs.index");
    }

    public function previewId($dialogId = null, Request $request)
    {
        $modalCss = view('scripts.modal-css')->render();

        $dialog = Dialog::where('id', '=', $dialogId)->first();

        $vars = [
            'modalCss' => (new Minify\CSS($modalCss))->minify(),
            'modalMessage' => $dialog->message,
            'modalImage' => $dialog->image,
            'modalButtonYes' => $dialog->button_allow,
            'modalButtonNo' => $dialog->button_block,
            'modalDelay' => $dialog->delay * 1000,
        ];

        $js = view('scripts.preview', $vars)->render();

        return response($js)
            ->header('Content-Type', 'text/javascript; charset=utf-8')
            ->header('Cache-Control', 'no-cache');
    }

    public function destroy($dialogId)
    {
        $dialog = Auth::user()->dialogs()->where('id', $dialogId)->first();

        if (! $dialog instanceof Dialog) {
            return $this->fail("Dialog not found.", 404);
        }

        $defaultDialogId = Auth::user()->dialogs()->where("is_global", '=', 1)->first()->id;

        foreach ($dialog->websites()->withTrashed()->get() as $website) {
            $website->dialog_id = $defaultDialogId;
            $website->save();
        }

        $dialog->delete();
        $dialog->refresh();

        return \Redirect::back()->with('success', 'Dialog deleted.');

        return $this->success($dialog, 200, null, "Dialog deleted.", "dialogs.index");
    }
}
