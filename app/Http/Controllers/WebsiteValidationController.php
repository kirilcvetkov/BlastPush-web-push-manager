<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class WebsiteValidationController extends Controller
{
    public function get(array $websites)
    {
        $websites = array_column($websites, "uuid");

        return Auth::user()->websites()->get()->filter(function ($website) use ($websites) {
            return in_array($website['uuid'], $websites);
        });
    }
}
