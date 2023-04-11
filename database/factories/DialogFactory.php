<?php

namespace Database\Factories;

use App\Models\Dialog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DialogFactory extends Factory
{
    protected $model = Dialog::class;

    public function definition()
    {
        return [
            "is_global" => false,
            "message" => '{$domain} wants to send you notifications',
            // "image" => "https://s3.amazonaws.com/blastpush.com/img/adventure-time.png",
            "image" => null,
            "delay" => 1,
            "button_allow" => "ALLOW",
            "button_block" => "BLOCK",
            "user_id" => User::where('email', '=', env('ADMIN_EMAIL'))->first()->id,
        ];
    }
}
