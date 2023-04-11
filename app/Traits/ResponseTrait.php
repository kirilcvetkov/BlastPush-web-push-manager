<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ResponseTrait
{
    public function success(
        $payload = [],
        int $httpCode = 200,
        string $view = null,
        string $message = null,
        string $redirect = null,
        string $parameter = null
    ) {
        if (request()->wantsJson()) {
            return response()->json($payload, $httpCode);
        }

        if (! is_null($redirect)) {
            return redirect()->route($redirect, $parameter)->with('success', $message ?: null);
        }

        return view($view, compact('payload', 'httpCode'))->with('success', $message ?: null);
    }

    public function fail($errors = null, int $httpCode = 500, string $redirect = null, string $parameter = null)
    {
        if (is_null($errors)) {
            switch ($httpCode) {
                case 400: $errors = ["message" => "Bad request."]; break;
                case 404: $errors = ["message" => "Not found."]; break;
                case 409: $errors = ["message" => "Duplicate."]; break;
                case 422: $errors = ["message" => "Unprocessable entity."]; break;
                default:
                case 500: $errors = ["message" => "Internal server error."]; break;
            }
        }

        if (is_string($errors)) {
            $errors = ['message' => $errors];
        }

        if (request()->wantsJson()) {
            return response()->json($errors, $httpCode);
        }

        if (! is_null($redirect)) {
            return redirect()->route($redirect, $parameter)->withInput()->withErrors($errors);
        }

        return redirect()->back()->withInput()->withErrors($errors);
    }
}
