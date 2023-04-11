<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition()
    {
        $user = User::where('email', '=', env('ADMIN_EMAIL'))->first();

        $types = config('constants.campaignTypes');
        $type = $types[array_rand($types)];

        return [
            'name' => ucfirst($this->faker->word()) . " " . ucfirst($this->faker->word()),
            'enabled' => rand(0, 10) == 10 ? false : true,
            'type' => $type,
            'user_id' => $user->id,
        ];
    }
}
