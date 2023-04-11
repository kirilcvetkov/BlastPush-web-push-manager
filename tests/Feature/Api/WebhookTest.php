<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Website;

final class WebhookTest extends TestCase
{
    use WithFaker;

    public function testWebhookIndex()
    {
        $response = $this->act()->getJson('api/website/' . $this->websiteUuid() . '/webhook');

        $response
            ->assertOk()
            ->assertHeader("content-type", "application/json")
            ->assertJsonStructure(['data' => [
                '*' => [
                    "id",
                    "created_at",
                    "updated_at",
                    "event_id",
                    "website_id",
                    "event_type_id",
                    "tries",
                    "request_url",
                    "request_body",
                    "request_method",
                    "response_status",
                    "response_headers",
                    "response_body",
                    "status",
                ]
            ]]);
    }

    public function testWebhookIndexSomeoneElses()
    {
        $response = $this->act()->getJson('api/website/' . $this->someoneElsesWebsiteId() . '/webhook');

        $response
            ->assertNotFound()
            ->assertHeader("content-type", "application/json");
    }
}
