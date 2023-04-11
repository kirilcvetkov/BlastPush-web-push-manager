<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Website;
use App\Models\Campaign;
use App\Models\Schedule;

class CampaignSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $userId = User::where('email', '=', env('ADMIN_EMAIL'))->first()->id;

        // Add only one waterfall campaign
        $campaign = Campaign::factory()->create([
            'name' => 'Default',
            'type' => 'waterfall',
            'user_id' => $userId,
        ]);

        $websites = Website::where('user_id', '=', $userId)->get();

        foreach ($websites->all() as $website) {
            $campaign->websites()->attach($website->id);
        }

        Schedule::factory()->create([
            'campaign_id' => $campaign->id,
            'delay' => 1,
            'order' => 0,
            'user_id' => $userId,
        ]);

        $types = config('constants.campaignTypes');
        unset($types[0]); // remove waterfall type

        $frequency = config('constants.campaignReoccurringFrequency');

        for ($i = 0; $i < 15; $i++) {
            $type = $types[array_rand($types)];

            $campaign = Campaign::factory()->create([
                'type' => $type,
                'user_id' => $userId,
            ]);

            foreach ($websites->random(rand(1, $websites->count()))->all() as $website) {
                $campaign->websites()->attach($website->id);
            }

            if ($type == 'scheduled') {
                Schedule::factory()->create([
                    'campaign_id' => $campaign->id,
                    'scheduled_at' => now()->addHours(rand(0, 100)),
                    'user_id' => $userId,
                ]);
            } else {
                $reoccurring = $frequency[array_rand($frequency)];
                $hour_minute = $reoccurring == "hourly"
                    ? $faker->time('00:i:00')
                    : $faker->time('H:i:s');
                switch ($reoccurring) {
                    case "hourly":
                    case "daily": $day = null; break;
                    case "weekly": $day = rand(0, 6); break;
                    case "monthly": $day = rand(0, 31); break;
                }

                Schedule::factory()->create([
                    'campaign_id' => $campaign->id,
                    'scheduled_at' => now()->addHours(rand(0, 100)),
                    'reoccurring_frequency' => $reoccurring,
                    'hour_minute' => $hour_minute,
                    'day' => $day,
                    'user_id' => $userId,
                ]);
            }
        }
    }
}
