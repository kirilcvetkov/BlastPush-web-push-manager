<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ApiTokenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('token', [
            'token' => Auth::user()->api_token,
            'new' => $request->input('new', 0)
        ]);
    }

    public function update(Request $request)
    {
        $token = Str::random(60);

        $request->user()->forceFill([
            'api_token' => $token,
        ])->save();

        return redirect()->route('account.show', ['new' => 1])
            ->with('success', 'API Access Token updated.');
    }
}
