<?php

namespace Database\Factories;

use App\Models\Website;
use App\Models\User;
use App\Models\Dialog;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebsiteFactory extends Factory
{
    protected $model = Website::class;

    public function definition()
    {
        $userId = User::where('email', '=', env('ADMIN_EMAIL'))->first()->id;
        $word1 = $this->faker->domainWord();
        $word2 = $this->faker->domainWord();
        $events = collect(array_keys(config('constants.eventTypesDetails')));

        return [
            "uuid" => (string)\Uuid::generate(4),
            "name" => ucfirst($word1) . " " . ucfirst($word2),
            "domain" => $word1 . "-" . $word2 . "." . $this->faker->tld(),
            "webhook_url" => "https://blastpush.com/api/some_webhook_url",
            "webhook_method" => config('constants.webhookMethod')['get'],
            "webhook_event_types" => $events->slice(1, rand(2, $events->count()))->toArray(),
            "dialog_id" => Dialog::where('is_global', 1)->where('user_id', $userId)->first()->id,
            "user_id" => $userId,
        ];
    }
}
