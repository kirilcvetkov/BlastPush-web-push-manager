<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiLogger
{
    private $startTime;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->startTime = microtime(true);
        if (! defined('LARAVEL_START')) {
            define('LARAVEL_START', microtime(true));
        }
        return $next($request);
    }

    public function terminate($request, $response)
    {
        if (! defined('LARAVEL_START')) {
            define('LARAVEL_START', microtime(true));
        }

        $headers = collect($request->headers)->mapWithKeys(function ($values, $header) {
            return [$header => join("; ", $values)];
        })->all();

        if (env("API_LOGGER", true)) {
            Log::channel('api')->debug(
                "URL: " . $request->fullUrl() . "\n" .
                "Method: " . $request->method() . "\n" .
                "Time: " . date("F j, Y, g:i a") . "\n" .
                "Duration: " . number_format(microtime(true) - LARAVEL_START, 3) . "\n" .
                "IP Address: " . $request->ip() . "\n" .
                "Headers: " . var_export($headers, true) . "\n" .
                "Input: " . str_replace("\n", " ", $request->getContent()) . "\n" .
                "Output: " . $this->parseOutput($response->getContent()) . "\n" .
                "\n" . str_repeat("=", 50) . "\n\n"
            );
        }
    }

    protected function parseOutput($response)
    {
        if (strpos($response, '"exception":')) {
            return substr($response, 0, strpos($response, '"trace":')) . "...";
        } elseif (strlen($response) > 1000) {
            return substr($response, 0, 1000) . "...";
        }
        return $response;
    }
}
