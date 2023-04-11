<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    protected $merge = [];

    public function __construct(Request $request, $id = null, $object = "message")
    {
        $this->merge = [
            'icon' => $request->icon,
            'image' => $request->image,
            'badge' => $request->badge,
            'sound' => $request->sound,
        ];

        $uploads = $request->validate([
            'iconupload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imageupload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'badgeupload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'soundupload' => 'nullable|mimes:mp3,mp4,mpga,wav|max:2048',
        ]);

        foreach ($uploads as $image => $upload) {
            if (! is_object($upload)) {
                continue;
            }

            $field = str_replace("upload", "", $image);

            $this->merge[$field] = env('AWS_URL') . "/" . $upload->storeAs(
                'img/' . $request->user()->uuid,
                join("_", [$object, $id, $field]) . "." . $upload->getClientOriginalExtension(),
                ['disk' => 's3', 'visibility' => 'public']
            );
        }

        return $this->merge;
    }

    public function get()
    {
        return $this->merge;
    }
}
