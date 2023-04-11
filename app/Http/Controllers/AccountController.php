<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;

class AccountController extends Controller
{
    public function show()
    {
        return view('account.show', ['user' => Auth::user()]);
    }

    public function profile()
    {
        return view('account.profile', ['user' => Auth::user()]);
    }

    public function plan()
    {
        return view('account.plan', [
            'plans' => Plan::where('available', true)->get()
            // 'intent' => Auth::user()->createSetupIntent()
        ]);
    }

    public function planStore(Request $request)
    {
        $user = Auth::user();

        $user->plan_id = $request->get('plan_id');

        $user->update();

        return redirect()->route('account.show')->with('success', 'Plan updated.');
    }

    public function profileUpdate(Request $request)
    {
        app('App\Http\Controllers\Admin\UserController')->update($request, Auth::user(), true);

        return redirect()->route('account.show')->with('success', 'Profile updated.');
    }
}
