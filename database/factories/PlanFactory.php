<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition()
    {
        return [
            'name' => 'Free',
            'details' => 'This is the free tier designed to let you evaluate our service. ' .
                'It will be available for a month only.',
            'website_limit' => 5,
            'message_limit' => 10,
            'subscriber_limit' => 20000,
            'push_limit' => 1000,
            'push_limit_timeframe' => 'daily',
            'cost' => 0.0,
            'can_renew' => false,
            'available' => true,
            'color' => 'secondary',
        ];
    }
}
