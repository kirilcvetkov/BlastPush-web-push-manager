<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use DB;
use Hash;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Redirect;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Users/Index', [
            'payload' => User::with('plan')
                ->withCount(['websites', 'subscribers', 'campaigns', 'messages', 'pushes'])
                ->paginate(25),
            'countries' => config('countries')->keyBy('iso'),
        ]);
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name')->all();
        $plans = Plan::where('available', true)->get();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('users.edit', compact('user', 'roles', 'plans', 'userRole'));
    }

    public function validation($user)
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            // 'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:2'],
            'website' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            // 'roles' => ['required']
            'plan_id' => ['sometimes', 'required', 'integer'],
        ];
    }

    public function update(Request $request, User $user, bool $internal = false)
    {
        $this->validate($request, $this->validation($user));

        $input = $request->all();

        $user->update($input);

        if ($internal) {
            return true;
        }

        DB::table('model_has_roles')->where('model_id', $user->id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function password(Request $request, User $user)
    {
        $this->validate($request, [
            'password' => 'required|confirmed|min:6',
        ]);

        $user->password = Hash::make($request->input('password'));

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        return redirect()->route('users.index')->with('success', 'User password reset.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return Redirect::back()->with('success', "User {$user->name} deleted.");
    }
}
