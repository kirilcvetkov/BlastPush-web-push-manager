<?php

namespace Database\Seeders;
use App\Models\Website;
use App\Models\User;
use App\Models\Dialog;

use Illuminate\Database\Seeder;

class WebsitesSeeder extends Seeder
{
    public function run()
    {
        // $adminId = User::where('email', '=', env('ADMIN_EMAIL'))->first()->id;
        // $notAdminId = User::where('email', '!=', env('ADMIN_EMAIL'))->first()->id;
        $users = User::take(5)->get();

        foreach ($users as $user) {
            $dialog = Dialog::factory()->create([
                "is_global" => true,
                "user_id" => $user->id,
            ]);

            Website::factory()->create([
                'name' => 'BlastPush',
                'domain' => 'blastpush.com',
                'user_id' => $user->id,
                'webhook_url' => 'https://blastpush.com/api/some_webhook_url',
                'webhook_event_types' => [1, 2],
                'dialog_id' => $dialog->id,
            ]);

            $dialog1 = Dialog::factory()->create([
                "is_global" => false,
                "message" => "Subscribe and receive offers as soon as they are available!",
                "image" => "https://s3.amazonaws.com/blastpush.com/img/adventure-time.png",
                "user_id" => $user->id,
            ]);

            Website::factory()->count(25)->create([
                'user_id' => $user->id,
                'dialog_id' => $dialog->id,
            ]);

            Website::factory()->count(25)->create([
                'user_id' => $user->id,
                'dialog_id' => $dialog1->id,
            ]);
        }
    }
}
