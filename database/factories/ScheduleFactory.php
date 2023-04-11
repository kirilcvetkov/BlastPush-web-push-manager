<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition()
    {
        $userId = User::where('email', '=', env('ADMIN_EMAIL'))->first()->id;
        $campaign = Campaign::where('user_id', '=', $userId)->get()->random();
        if (! $campaign instanceof Campaign) {
            $campaign = Campaign::factory()->create();
        }
        $message = Message::where('user_id', '=', $userId)->get()->random();

        return [
            'delay' => rand(0, 30),
            'order' => rand(0, 10),
            'campaign_id' => $campaign->id,
            'message_id' => $message->id,
            'user_id' => $userId,
        ];
    }
}
