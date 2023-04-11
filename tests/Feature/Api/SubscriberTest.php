<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Subscriber;

final class SubscriberTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Subscriber::unsetEventDispatcher();
    }

    public function testSubscriberCreate()
    {
        $fields = $this->subscription(factory(Subscriber::class)->make()->toArray());

        $response = $this->act()->postJson(
            "/api/website/" . $this->websiteUuid() . "/subscriber",
            $fields
        );

        $response
            ->assertSuccessful()
            ->assertHeader("content-type", "application/json")
            ->assertJsonFragment([
                "website_id" => $this->websiteId(),
                "endpoint" => $fields['subscription']['endpoint']
            ]);
    }

    public function testSubscriberDelete()
    {
        $sub = Subscriber::where('website_id', '=', $this->websiteId())->whereNull('deleted_at')->get()->random();
        $fields = $this->subscription($sub);

        $response = $this->deleteJson("/api/website/" . $this->websiteUuid() . "/subscriber", $fields);

        $response
            ->assertOk()
            ->assertHeader("content-type", "application/json")
            ->assertJsonFragment([
                "subscribed" => 0,
                "endpoint" => $fields['subscription']['endpoint'],
                "website_id" => $this->websiteId(),
            ]);

        $this->assertNotNull($response->json("deleted_at"));
    }

    public function testSubscriberDeleteNonExistent()
    {
        $fields = $this->subscription(factory(Subscriber::class)->make()->toArray());

        $response = $this->deleteJson("/api/website/" . $this->websiteUuid() . "/subscriber", $fields);

        $response
            ->assertNotFound()
            ->assertHeader("content-type", "application/json")
            ->assertSee("Not found");
    }

    public function testSubscriberList()
    {
        $response = $this->act()->getJson("/api/website/" . $this->websiteUuid() . "/subscriber");

        $response
            ->assertOk()
            ->assertJsonFragment(["website_id" => $this->websiteId()]);
    }

    public function testSubscriberListSomeoneElses()
    {
        $response = $this->act()->getJson("/api/website/" . $this->someoneElsesWebsiteId() . "/subscriber");

        $response
            ->assertNotFound()
            ->assertSee("not found");
    }
}
