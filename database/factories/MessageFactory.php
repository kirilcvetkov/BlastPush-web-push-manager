<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            "title" => $this->faker->sentence(4),
            "url" => $this->faker->url(),
            "body" => substr($this->faker->sentence(6), 0, -1),
            "button" => "Click Here",
            "icon" => "https://s3.amazonaws.com/blastpush.com/img/airplane.png",
            "image" => "https://s3.amazonaws.com/blastpush.com/img/airplane.png",
            "badge" => "https://s3.amazonaws.com/blastpush.com/img/airplane.png",
            "sound" => "https://s3.amazonaws.com/blastpush.com/sounds/ding.mp3",
            "direction" => "auto",
            "actions" => null,
            "silent" => false,
            "tag" => null,
            "renotify" => false,
            "require_interaction" => true,
            "user_id" => User::where('email', '=', env('ADMIN_EMAIL'))->first()->id,
        ];
    }
}
