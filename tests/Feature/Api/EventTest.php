<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Events\WebhookCreated;
use App\Models\Push;
use App\Models\Event;
use App\Models\User;
use App\Models\Website;
use App\Models\Subscriber;

final class EventTest extends TestCase
{
    use WithFaker;

    public function testEventAllTypes()
    {
        // $this->expectsEvents(WebhookCreated::class);

        $push = Push::factory()->create([
            "website_id" => $this->websiteId(),
            "subscriber_id" => $this->subscribers()->random()['id'],
        ]);

        foreach (config('constants.eventTypesArr') as $type => $typeId) {
            if ($typeId == 0) {
                continue;
            }

            $post = [
                "type" => $type,
                "uuid" => $push->uuid,
                "message_id" => $push->message_id
            ];

            $response = $this->withHeaders([
                    'User-Agent' => $this->faker->userAgent(),
                    'Referer' => 'unit_testing',
                ])
                ->postJson("/api/website/" . $this->websiteUuid() . "/event", $post);

            $response->assertSuccessful()
                ->assertHeader("content-type", "application/json")
                ->assertJsonFragment([
                    "website_id" => $this->websiteId(),
                    "type_id" => $typeId,
                    "type" => $type,
                    "uuid" => $push->uuid,
                ]);
        }
    }

    public function testEventShow()
    {
        $event = collect(Event::where('user_id', '=', $this->user()->id)->get())->random();

        $response = $this->act()->getJson("/api/events/" . $event->id);

        $response
            ->assertSuccessful()
            ->assertJsonFragment(["id" => $event->id, "type_id" => $event->type_id])
            ->assertJsonStructure([
                "id",
                "type_id",
                "type",
                "uuid",
                "url",
                "location",
                "ip",
                "user_agent",
                "device",
                "platform",
                "browser",
                "referer",
                "body",
                "website_id",
            ]);
    }

    public function testEventShowSomeoneElses()
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        $subscriber = Subscriber::factory()->create([
            'user_id' => $user->id,
            'website_id' => $website->id,
        ]);
        $event = Event::factory()->create([
            'user_id' => $user->id,
            'website_id' => $website->id,
            'subscriber_id' => $subscriber->id,
        ]);

        $response = $this->act()->getJson("/api/events/" . $event->id);

        $response
            ->assertNotFound()
            ->assertHeader("content-type", "application/json")
            ->assertSee("Event not found");
    }

    public function testEventShowUnauthorized()
    {
        $event = collect(Event::where('user_id', '=', $this->user()->id)->get())->random();

        $response = $this->getJson("/api/events/" . $event->id);

        $response
            ->assertUnauthorized()
            ->assertHeader("content-type", "application/json");
    }

    public function testEventIndex()
    {
        $response = $this->act()->getJson("/api/website/" . $this->websiteUuid() . "/event");

        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data' => [
                '*' => [
                    "id",
                    "type_id",
                    "type",
                    "uuid",
                    "url",
                    "location",
                    "ip",
                    "user_agent",
                    "device",
                    "platform",
                    "browser",
                    "referer",
                    "body",
                    "website_id",
                ]
            ]]);
    }

    public function testEventIndexUnauthorized()
    {
        $response = $this->getJson("/api/website/" . $this->websiteUuid() . "/event");

        $response
            ->assertUnauthorized()
            ->assertHeader("content-type", "application/json");
    }

    public function testEventIndexSomeoneElsesWebsite()
    {
        $response = $this->getJson("/api/website/" . $this->someoneElsesWebsiteId() . "/event");

        $response
            ->assertUnauthorized()
            ->assertHeader("content-type", "application/json");
    }

    public function testEventIndexWebisteNotFound()
    {
        $randomUuid = (string)\Uuid::generate(4);

        $response = $this->act()->getJson("/api/website/{$randomUuid}/event");

        $response
            ->assertNotFound()
            ->assertHeader("content-type", "application/json")
            ->assertSee("Website not found");
    }
}
