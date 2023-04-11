<?php

namespace Database\Factories;

use App\Models\Push;
use App\Models\User;
use App\Models\Website;
use App\Models\Message;
use App\Models\Subscriber;
use App\Models\Campaign;
use App\Models\Schedule;

use Illuminate\Database\Eloquent\Factories\Factory;

class PushFactory extends Factory
{
    protected $model = Push::class;

    public function definition()
    {
        $user = User::where('email', '=', env('ADMIN_EMAIL'))->first();
        $website = Website::where('domain', '=', 'blastpush.com')->first();
        $message = Message::where('user_id', '=', $user->id)->get()->random();
        $subscriber = Subscriber::where('user_id', '=', $user->id)->get()->random();
        $campaign = Campaign::where('user_id', '=', $user->id)->get()->random();
        $schedule = $campaign->schedules->random();

        if (! $schedule instanceof Schedule) {
            Schedule::factory()->create([
                'campaign_id' => $campaign->id,
            ]);
        }

        return [
            "created_at" => date('Y-m-d H:i:s', rand(strtotime("-7 days"), time())),
            "user_id" => $user->id,
            "website_id" => $website->id,
            "subscriber_id" => $subscriber->id,
            "message_id" => $message->id,
            "campaign_id" => $campaign->id,
            "schedule_id" => $schedule->id,
            "uuid" => (string)\Uuid::generate(4),
            "scheduled_to_send_at" => $this->faker->dateTimeBetween("+1 minute", "+14 days"),
            "sent_at" => $this->faker->dateTimeBetween("+1 minute", "+14 days"),
            "is_success" => 1,
            "response" => 'OK',
            "http_code" => 201,
        ];
    }
}
