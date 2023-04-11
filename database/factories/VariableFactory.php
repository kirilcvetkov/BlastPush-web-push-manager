<?php

namespace Database\Factories;

use App\Models\Variable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VariableFactory extends Factory
{
    protected $model = Variable::class;

    public function definition()
    {
        $user = User::where('email', '=', env('ADMIN_EMAIL'))->first();

        $scope = config('constants.variableScopes');
        $scope = $scope[array_rand($scope)];

        switch ($scope) {
            case 'global':
                $targetId = null;
                break;
            case 'website':
                $targetId = $user->websites()->get()->random();
                break;
            case 'subscriber':
                $targetId = $user->subscribers()->get()->random();
                break;
            case 'campaign':
                $targetId = $user->campaigns()->get()->random();
                break;
            case 'schedule':
                $campaign = $user->campaigns()->get()->random();
                $targetId = $campaign->schedules()->get()->random();
                break;
            case 'message':
                $targetId = $user->messages()->get()->random();
                break;
            case 'push':
                $targetId = $user->pushes()->get()->random();
                break;
        }

        return [
            'name' => strtolower($this->faker->word()),
            'scope' => $scope,
            'value' => strtolower($this->faker->word()),
            'target_id' => $targetId,
            'user_id' => $user->id,
        ];
    }
}
