<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Website;
use App\Models\Webhook;
use App\Models\Event;
use App\Listeners\ProcessWebhook;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookFactory extends Factory
{
    protected $model = Webhook::class;

    public function definition()
    {
        $userId = User::where('email', '=', env('ADMIN_EMAIL'))->first()->id;
        $website = Website::where('user_id', '=', $userId)->first();
        $event = Event::where('user_id', '=', $userId)
            ->where('website_id', '=', $website->id)
            ->orderBy('id', 'desc')
            ->first();

        $delays = (new ProcessWebhook())->delays;

        return [
            "website_id" => $website->id,
            "event_id" => $event->id,
            "event_type_id" => $event->type_id,
            "tries" => array_rand($delays),
            "request_url" => "https://blastpush.com/api/some_webhook_url",
            "request_method" => config('constants.webhookMethod')['get'],
            "request_body" => json_encode([]),
            "response_status" => 200,
            "response_headers" => json_encode([]),
            "response_body" => json_encode([]),
            "status" => config('constants.webhookResponseStatus')['success'],
        ];
    }
}
