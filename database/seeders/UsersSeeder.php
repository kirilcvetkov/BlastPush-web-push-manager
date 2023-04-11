<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use App\Models\Plan;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $listing = [];

        $fullAccess = [
            $listing[] = 'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            $listing[] = 'plan-list',
            'plan-create',
            'plan-edit',
            'plan-delete',

            'superuser',
        ];

        foreach ($fullAccess as $role) {
            Permission::create(['name' => $role]);
        }

        $admins = Role::create(['name' => 'Admin']);
        $users = Role::create(['name' => 'User']);
        $plans = Plan::get();

        // $admins->syncPermissions($fullAccess); // Admin is allowed everywhere using Gate
        $users->syncPermissions($listing);

        $admin = User::factory()->create([
            'first_name' => 'Kiril',
            'last_name' => 'Cvetkov',
            'email' => env('ADMIN_EMAIL'),
            'password' => bcrypt(env('ADMIN_PASSWORD')),
            'phone' => '312444555',
            'country' => 'US',
            'website' => 'https://blastpush.com',
            'company' => 'blastpush.com',
            'timezone' => 'America/Chicago',
            'api_token' => '5xFNAOwNP64ew8DXea0Cl50fBdY8XN0QSxxbtUyRRpvTTyWgIaWRnK1nY4xQ',
            'plan_id' => $plans->last()->id,
        ]);
        $admin->assignRole($admins);

        $user = User::factory()->create([
            'first_name' => 'Kiril',
            'last_name' => 'Slicksky',
            'email' => 'kiril@slicksky.com',
            'password' => bcrypt(env('ADMIN_PASSWORD')),
            'phone' => '312444555',
            'country' => 'US',
            'website' => 'https://slicksky.com',
            'company' => 'slicksky.com',
            'timezone' => 'America/Chicago',
            'api_token' => '1kiOpndNkOGgE4ZITyXWG39jTOzSWjm7kCxprKskFoREHBVmjffAtLiAXNBD',
            'plan_id' => $plans->last()->id,
        ]);
        $user->assignRole($users);

        User::factory()->count(25)->create()->each(function ($item) use ($users) {
            $item->assignRole($users);
        });
    }
}
