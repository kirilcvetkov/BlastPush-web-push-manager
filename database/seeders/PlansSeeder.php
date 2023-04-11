<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlansSeeder extends Seeder
{
    public function run()
    {
        Plan::factory()->create([
            'name' => 'Bronze',
            'details' => 'Lorem ipsum minim ut cillum velit velit non aliquip adipisicing consequat nisi laborum ullamco incididunt do exercitation in non.',
            'website_limit' => 100,
            'message_limit' => 1000,
            'subscriber_limit' => 30000,
            'push_limit' => 2000,
            'push_limit_timeframe' => 'daily',
            'cost' => 95.0,
            'can_renew' => true,
            'available' => true,
            'color' => 'warning',
        ]);

        Plan::factory()->create([
            'name' => 'Silver',
            'details' => 'Lorem ipsum minim ut cillum velit velit non aliquip adipisicing consequat nisi laborum ullamco incididunt do exercitation in non.',
            'website_limit' => 1000,
            'message_limit' => 10000,
            'subscriber_limit' => 40000,
            'push_limit' => 5000,
            'push_limit_timeframe' => 'daily',
            'cost' => 110.0,
            'can_renew' => true,
            'available' => true,
            'color' => 'success',
        ]);

        Plan::factory()->create([
            'name' => 'Gold',
            'details' => 'Lorem ipsum minim ut cillum velit velit non aliquip adipisicing consequat nisi laborum ullamco incididunt do exercitation in non.',
            'website_limit' => 2000,
            'message_limit' => 20000,
            'subscriber_limit' => 50000,
            'push_limit' => 10000,
            'push_limit_timeframe' => 'daily',
            'cost' => 125.0,
            'can_renew' => true,
            'available' => true,
            'color' => 'info',
        ]);

        Plan::factory()->create([
            'name' => 'Platinium',
            'details' => 'Lorem ipsum minim ut cillum velit velit non aliquip adipisicing consequat nisi laborum ullamco incididunt do exercitation in non.',
            'website_limit' => 5000,
            'message_limit' => 50000,
            'subscriber_limit' => 60000,
            'push_limit' => 50000,
            'push_limit_timeframe' => 'daily',
            'cost' => 140.0,
            'can_renew' => true,
            'available' => true,
            'color' => 'primary',
        ]);
    }
}
