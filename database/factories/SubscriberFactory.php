<?php

namespace Database\Factories;

use App\Models\Subscriber;
use App\Models\User;
use App\Models\Website;
use Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriberFactory extends Factory
{
    protected $model = Subscriber::class;

    public function definition()
    {
        $user = User::where('email', '=', env('ADMIN_EMAIL'))->first();
        $website = Website::where('user_id', '=', $user->id)->first();

        $body = [
            "type" => "subscribe",
            "uuid" => null,
            "subscription" => [
                "endpoint" => "https://updates.push.services.mozilla.com/wpush/v1/" . Str::random(100),
                "keys" => [
                    "auth" => Str::random(20),
                    "p256dh" => Str::random(50),
                ]
            ],
            "encoding" => "aesgcm",
            "location" => urlencode("http://localhost/push"),
        ];

        $subscribed = rand(0, 10) == 10 ? 0 : 1;

        return [
            "created_at" => date('Y-m-d H:i:s', rand(strtotime("-7 days"), time())),
            "endpoint" => $body["subscription"]["endpoint"],
            "hash" => md5($body["subscription"]["endpoint"]),
            "expiration" => null,
            "public" => $body["subscription"]["keys"]["p256dh"],
            "auth" => $body["subscription"]["keys"]["auth"],
            "encoding" => $body["encoding"],
            "body" => json_encode($body, JSON_PRETTY_PRINT),
            "website_id" => $website->id,
            "user_id" => $user->id,
            "subscribed" => $subscribed,
            "deleted_at" => $subscribed ? null : time(),
        ];
    }
}
