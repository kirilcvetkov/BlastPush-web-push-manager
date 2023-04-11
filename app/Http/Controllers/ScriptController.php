<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MatthiasMullie\Minify;
use App\Traits\ResponseTrait;
use App\Models\Website;

class ScriptController extends Controller
{
    use ResponseTrait;

    public function index($websiteUuid)
    {
        $website = Auth::user()->websites()->where('uuid', $websiteUuid)->first();

        if (! $website instanceof Website) {
            return $this->fail("Website not found.", 404);
        }

        return view('scripts', compact('website'));
    }

    /**
     * Download JS files
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function download(Website $website)
    {
        $name = strtolower(preg_replace("/[^a-z0-9\.]/i", "", $website->name));
        $file = sys_get_temp_dir() . "/{$name}.zip";

        $zip = new \ZipArchive();
        $zip->open($file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zip->addFromString(
            "push.js",
            $this->wrap($website, 'api.javascript', true)
        );

        $zip->addFromString(
            "sw.js",
            $this->wrap($website, 'api.serviceworker', true)
        );

        $zip->close();

        return response()->download($file);
    }

    /**
     * Generate website JS.
     *
     * @param  \App\Website  $website
     * @return \Illuminate\Http\Response
     */
    public function javascript(Website $website, bool $download = false)
    {
        $local = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
        $modalCss = view('scripts.modal-css')->render();

        if (isset($website->dialog->message)) {
            $website->dialog->message = str_replace('{$domain}', $website->domain, $website->dialog->message);
        }

        $vars = [
            'vapid' => $website->vapid_public ?? $website->user->vapid_public,
            'apiSubscriber' => route('api.subscriber', ['website' => $website]),
            'swJs' => $local ? request()->url . '/sw-local.js' : 'sw.js',
            'modalCss' => (new Minify\CSS($modalCss))->minify(),
            'modalMessage' => $website->dialog->message ?: "Receive offers as soon as they are available!",
            'modalImage' => $website->dialog->image ?? "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' " .
                "xmlns:xlink='http://www.w3.org/1999/xlink' version='1.1' width='24' height='24' viewBox='0 0 24 24' " .
                "fill='rgb(41, 121, 255)'><path d='M21,19V20H3V19L5,17V11C5,7.9 7.03,5.17 10,4.29C10,4.19 10,4.1 " .
                "10,4C10,2.9 10.9,2 12,2C13.1,2 14,2.9 14,4C14,4.1 14,4.19 14,4.29C16.97,5.17 19,7.9 19,11V17L21," .
                "19M14,21C14,22.1 13.1,23 12,23C10.9,23 10,22.1 10,21' /></svg>",
            'modalButtonYes' => $website->dialog->button_allow ?: "ALLOW",
            'modalButtonNo' => $website->dialog->button_block ?: "BLOCK",
            'modalDelay' => $website->dialog->delay * 1000,
            'showPercentage' => $website->dialog->show_percentage,
            'emitEvents' => $website->id === 1 ? 1 : 0,
            'bpHub' => $local ? 'https://blastpush.test/hub' : 'https://blastpush.com/hub',
            'bpUserUuid' => $website->user->uuid,
            'apiEvent' => route('api.event', ['website' => $website]),
            'dedupe' => $website->dedupe_subscribers
        ];

        $js = view('scripts.push-js', $vars)->render();

        if (! $local) {
            $js = (new Minify\JS($js))->minify();
        }

        if ($download) {
            return $js;
        }

        return response($js)
            ->header('Content-Type', 'application/javascript; charset=utf-8')
            ->header('Cache-Control', 'no-cache');
    }

    /**
     * Generate server-side service worker JS.
     *
     * @param  \App\Website  $website
     * @return \Illuminate\Http\Response
     */
    public function serviceworker(Website $website, bool $download = false)
    {
        $local = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
        $vars = [
            'apiEvent' => route('api.event', ['website' => $website]),
            'apiPayload' => route('api.payload', ['website' => $website]),
            'apiIcon' => route('api.icon', ['website' => $website]),
            'websiteUuid' => $website->uuid,
            'verbose' => $website->id === 1 ? 1 : 0,
        ];

        $js = // $local
            // ? view('scripts.sw', $vars)->render()
            // :
            view('scripts.serviceworker-js', $vars)->render();

        if (! $local) {
            $js = (new Minify\JS($js))->minify();
        }

        if ($download) {
            return $js;
        }

        return response($js)
            ->header('Content-Type', 'application/javascript; charset=utf-8')
            ->header('Cache-Control', 'no-cache');
    }

    /**
     * Wrap JS scripts and import them directly from the API.
     *
     * @param  \App\Website  $website
     * @return \Illuminate\Http\Response
     */
    public function wrap(Website $website, $route, bool $download = false, array $parameters = [])
    {
        switch ($route) {
            case 'api.javascript':
                $js = "(function(document, window) {" .
                    "var head = document.getElementsByTagName('head')[0];" .
                    "var script = document.createElement('script');" .
                    "script.type = 'application/javascript';" .
                    "script.src = '" . route($route, ['website' => $website] + $parameters) . "';" .
                    "head.appendChild( script );" .
                    "})(document, window);";
                break;
            case 'api.serviceworker':
                $js = 'self.importScripts("' . route($route, ['website' => $website] + $parameters) . '");';
                break;
        }

        $js = (new Minify\JS($js))->minify();

        if ($download) {
            return $js;
        }

        return response($js)
            ->header('Content-Type', 'application/javascript; charset=utf-8')
            ->header('Cache-Control', 'no-cache');
    }
}
