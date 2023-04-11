<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Traits\ResponseTrait;
use Inertia\Inertia;

class MessageController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        if (request()->wantsJson()) {
            return $this->success(
                Auth::user()->messages()->orderBy('id')->paginate(25),
                200,
                "messages.index"
            );
        }

        return Inertia::render('Message/Index', [
            'payload' => Auth::user()->messages()
                ->withCount('pushes')
                ->orderBy('id')
                ->paginate(25),
        ]);
    }

    public function create()
    {
        return $this->edit(new Message());
    }

    public function store(Request $request)
    {
        return $this->update($request);
    }

    public function show(Message $message)
    {
        //
    }

    public function edit(Message $message)
    {
        return Inertia::render('Message/Create', $message->toArray()); // + compact('campaignsList', 'types'));
    }

    public function validation()
    {
        return [
            'title' => ['required'],
            'url' => ['required'],
            'body' => ['required'],
            'button' => ['required'],
            'icon' => ['nullable', 'url'],
            'image' => ['nullable', 'url'],
            'badge' => ['nullable', 'url'],
            'sound' => ['nullable', 'url'],
            'direction' => [Rule::in(config('constants.direction'))],
            'actions' => ['nullable', 'array'],
            'silent' => ['boolean'],
            'tag' => ['nullable', 'string'],
            'ttl' => ['nullable', 'integer'],
            'renotify' => ['boolean'],
            'require_interaction' => ['nullable', 'boolean'],
        ];
    }

    public function update(Request $request, $messageId = null)
    {
        $request->merge((new ImageUploadController($request, $messageId))->get());

        $validation = Validator::make(
            $request->json()->all() ?: $request->all(),
            $this->validation()
        );

        if ($validation->fails()) {
            return $this->fail($validation->errors(), 422);
        }

        $message = is_null($messageId)
            ? new Message()
            : Auth::user()->messages()->where(['id' => $messageId])->first();


        if (! $message instanceof Message) {
            return $this->fail("Message not found", 404);
        }

        $message->fill($request->json()->all() ?: $request->all());
        $message->user_id = Auth::id();
        $message->save();
        $message->refresh();

        $title = $request->json()->get('title') ?: $request->get('title');

        return $this->success(
            $message,
            (is_null($messageId) ? 201 : 200),
            null,
            "Message {$title} " . (is_null($messageId) ? "created." : "updated."),
            "messages.index"
        );
    }

    public function destroy(int $messageId)
    {
        $message = Auth::user()->messages()->where(['id' => $messageId])->first();

        if (! $message instanceof Message) {
            return $this->fail("Message not found.", 404);
        }

        $message->delete();
        $message->refresh();

        return $this->success($message, 200, null, "Message deleted.", "messages.index");
    }
}
