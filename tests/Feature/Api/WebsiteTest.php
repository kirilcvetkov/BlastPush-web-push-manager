<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Website;

final class WebsiteTest extends TestCase
{
    use WithFaker;

    protected function list($raw = false)
    {
        $list = $this->act()->getJson('/api/website');

        if ($raw) {
            return $list;
        }

        return collect($list->json()['data'])->last();
    }

    public function testWebsiteIndex()
    {
        $response = $this->list(true);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [
                '*' => [
                    "name",
                    "domain",
                    "webhook_url",
                    "webhook_method",
                    "webhook_event_types",
                    "created_at",
                    "updated_at",
                    "deleted_at",
                ]
            ]]);
    }

    public function testWebsiteCreateMinimum()
    {
        $website = factory(Website::class)->make();

        $website = [
            "name" => $website->name,
            "domain" => $website->domain,
        ];

        $response = $this->act()->postJson('/api/website', $website);

        $response
            ->assertCreated()
            ->assertHeader("content-type", "application/json")
            ->assertJson($website, $response->getContent());
    }

    public function testWebsiteCreate()
    {
        $website = factory(Website::class)->make()->toArray();

        $response = $this->act()->postJson('/api/website', $website);

        $response
            ->assertCreated()
            ->assertHeader("content-type", "application/json")
            ->assertJson([
                "name" => $website['name'],
                "domain" => $website['domain'],
            ], $response->getContent());
    }

    public function testWebsiteUpdate()
    {
        $uuid = $this->list()['uuid'];
        $website = factory(Website::class)->make()->toArray();
        $website['uuid'] = $uuid;

        $response = $this->act()->putJson('/api/website/' . $uuid, $website);

        $response
            ->assertOk()
            ->assertHeader("content-type", "application/json")
            ->assertJson($website, $response->getContent());
    }

    public function testWebsiteUpdateSomeoneElses()
    {
        $website = factory(Website::class)->make()->toArray();

        $response = $this->act()->putJson('/api/website/' . $this->someoneElsesWebsiteId(), $website);

        $response
            ->assertNotFound()
            ->assertHeader("content-type", "application/json");
    }

    public function testWebsiteDuplicate()
    {
        $website = $this->list();

        $response = $this->act()->postJson('/api/website', [
            "name" => $website['name'],
            "domain" => $website['domain'],
        ]);

        $response
            ->assertStatus(422)
            ->assertSee('already been taken')
            ->assertHeader("content-type", "application/json");
    }

    public function testWebsiteDuplicateSomeoneElses()
    {
        $this->someoneElsesWebsiteId(); // Init $this->website

        $website = [
            "name" => $this->website->name,
            "domain" => $this->website->domain,
        ];

        $response = $this->act()->postJson('/api/website', $website);

        $response
            ->assertCreated()
            ->assertHeader("content-type", "application/json")
            ->assertJson($website, $response->getContent());
    }

    public function testWebsiteDelete()
    {
        $uuid = $this->list()['uuid'];

        $response = $this->act()->deleteJson('/api/website/' . $uuid);

        $response
            ->assertOk()
            ->assertHeader("content-type", "application/json");
    }

    public function testWebsiteDeleteSomeoneElses()
    {
        $response = $this->act()->deleteJson('/api/website/' . $this->someoneElsesWebsiteId());

        $response
            ->assertNotFound()
            ->assertHeader("content-type", "application/json");
    }
}
