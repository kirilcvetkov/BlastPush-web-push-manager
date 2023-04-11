<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class ETag
{
    public function handle($request, Closure $next)
    {
        // Get response
        $response = $next($request);

        if ($this->notApplicable($request)) {
            return $response;
        }

        // Get the initial method sent by client
        $initialMethod = $request->method();

        // Force to get in order to receive content
        $request->setMethod('get');

        // Generate Etag
        $etag = $this->generate($response);

        // Load the Etag sent by client
        $requestEtag = str_replace(['"', 'W/'], '', $request->getETags());

        // Check to see if Etag has changed
        if ($requestEtag && $requestEtag[0] == $etag) {
            $response->setNotModified();
        }

        // Set Etag
        $response->setEtag($etag);

        // Set back to original method
        $request->setMethod($initialMethod); // set back to original method

        // Send response
        return $response
            ->header('Cache-Control', 'no-cache, no-transform, must-revalidate, max-age=3600');
    }

    protected function notApplicable($request)
    {
        if (! $request->expectsJson()) {
            return true;
        }

        $method = strtolower($request->method());

        if ($method !== 'get' && $method !== 'head') {
            return true;
        }

        return false;
    }

    protected function generate($response): string
    {
        return md5(
            json_encode($response->headers->get('origin')) .
            config('constants.jsVersion') .
            $response->getContent()
        );
    }
}
