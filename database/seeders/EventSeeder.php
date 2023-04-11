<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscriber;
use App\Models\Event;
use App\Models\Webhook;
use Faker\Factory as Faker;

class EventSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        Webhook::flushEventListeners();

        for ($i = 0; $i < 250; $i++) {
            $subscriber = Subscriber::factory()->create();
            $body = json_decode($subscriber->body);

            $types = config('constants.eventTypesArr');
            $priorityTypes = [
                "subscribe" => 1,
                "unsubscribe" => 2,
                "visit" => 3,
                "notification-delivered" => 4,
                "notification-clicked" => 5,
                "notification-closed" => 6,
                "subscription-error" => 8,
                "permission-denied" => 9,
            ];
            $type = rand(0, 1) ? array_rand($types) : array_rand($priorityTypes);
            $type_id = $types[$type];

            $event = Event::factory()->create([
                'created_at' => date('Y-m-d H:i:s', rand(strtotime("-7 days"), time())),
                'type_id' => $type_id,
                'type' => $type,
                'subscriber_id' => $subscriber->id,
                'location' => $body->location,
                'body' => $subscriber->body,
                'website_id' => $subscriber->website_id,
                'user_id' => $subscriber->user_id,
            ]);

            $isError = rand(0, 1);

            $statusRand = array_rand(config('constants.webhookResponseStatus'));
            $status = $isError
                ? config('constants.webhookResponseStatus')['fail']
                : config('constants.webhookResponseStatus')[$statusRand];

            Webhook::factory()->create([
                'website_id' => $subscriber->website_id,
                'event_id' => $event->id,
                'event_type_id' => $event->type_id,
                'response_status' => $isError ? 404 : 200,
                'status' => $status,
            ]);
        }
    }
}
