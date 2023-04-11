<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\User;

class MessagesSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();

        Message::create([
            "title" => "Your flight is delayed",
            "url" => "https://www.southwest.com/",
            "body" => "Flight # 526 is delayed by 30 minutes and it will begin boarding at 10 am on Feb. 10th",
            "button" => "View flight",
            "icon" => "https://s3.amazonaws.com/blastpush.com/img/southwest.jpg",
            "image" => "https://s3.amazonaws.com/blastpush.com/img/southwest.jpg",
            "badge" => "https://s3.amazonaws.com/blastpush.com/img/southwest.jpg",
            "sound" => "https://s3.amazonaws.com/blastpush.com/sounds/ding.mp3",
            "user_id" => $user->id,
        ]);

        Message::create([
            "title" => "New mail from John",
            "url" => "https://gmail.com",
            "body" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
            "button" => "View message",
            "icon" => "https://s3.amazonaws.com/blastpush.com/img/angry-birds.png",
            "image" => "https://s3.amazonaws.com/blastpush.com/img/angry-birds.png",
            "badge" => "https://s3.amazonaws.com/blastpush.com/img/angry-birds.jpg",
            "sound" => "https://s3.amazonaws.com/blastpush.com/sounds/ding.mp3",
            "user_id" => $user->id,
        ]);

        Message::create([
            "title" => "Get a cash loan now - Easy as 1-2-3",
            "url" => "https://loannook.com",
            "body" => "At LoanNook we have established a large network of lenders to help you connect with the funds you need for your short-term needs.",
            "button" => "Get cash",
            "icon" => "https://s3.amazonaws.com/blastpush.com/img/loannook.jpg",
            "image" => "https://s3.amazonaws.com/blastpush.com/img/loannook.jpg",
            "badge" => "https://s3.amazonaws.com/blastpush.com/img/loannook.jpg",
            "sound" => "https://s3.amazonaws.com/blastpush.com/sounds/chaching.mp3",
            "user_id" => $user->id,
        ]);

        Message::factory()->count(50)->create();
    }
}
