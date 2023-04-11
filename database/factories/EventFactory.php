<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use App\Models\Subscriber;
use App\Models\Website;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        $user = User::where('email', '=', env('ADMIN_EMAIL'))->first();
        $subscriber = Subscriber::where('user_id', '=', $user->id)->get()->random();
        $website = Website::where('user_id', '=', $user->id)->first();

        $body = json_decode($subscriber->body);

        $types = config('constants.eventTypesArr');
        $type = array_rand($types);
        $type_id = $types[$type];

        $browser = browser($userAgent = $this->faker->userAgent);

        return [
            'type_id' => $type_id,
            'type' => $type,
            'location' => $body->location,
            'ip' => $this->faker->ipv4,
            'user_agent' => $userAgent,
            'device' => $browser->device(),
            'platform' => $browser->platform(),
            'browser' => $browser->browser(),
            'is_mobile' => $browser->isMobile(),
            'is_tablet' => $browser->isTablet(),
            'is_desktop' => $browser->isDesktop(),
            'is_robot' => $browser->isRobot(),
            'referer' => $this->faker->url,
            'body' => $subscriber->body,
            'uuid' => $this->faker->uuid,
            'website_id' => $website->id,
            'subscriber_id' => $subscriber->id,
            'user_id' => $user->id,
        ];
    }
}
