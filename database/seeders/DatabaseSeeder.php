<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PlansSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(WebsitesSeeder::class);
        $this->call(MessagesSeeder::class);
        $this->call(SubscriberSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(CampaignSeeder::class);
        $this->call(PushSeeder::class);
    }
}
