<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

final class PushTest extends TestCase
{
    use WithFaker;

    protected $websiteId;
    protected $subscriber;
    protected $messageId;

    protected function getSubscriberAndMessage(): void
    {
        $this->act = $this->actingAs(User::first(), 'api');

        if (empty($this->websiteId)) {
            $websites = $this->act->json("GET", "/api/website")->json();
            $this->websiteId = collect($websites)->where('domain', 'blastpush.com')->first()['uuid'];
        }

        if (empty($this->subscriber) and ! empty($this->websiteId)) {
            $subscribers = $this->act->json("GET", "/api/website/{$this->websiteId}/subscriber")->json();
            $this->subscriber = collect($subscribers)->filter(function ($item) {
                return stristr($item['endpoint'], 'test') === false;
            })->last();
        }

        if (empty($this->messageId)) {
            $messages = $this->act->json("GET", "/api/message")->json();
            $this->messageId = end($messages)['id'];
        }
    }

    // public function testPushCreate()
    // {
    //     $this->getSubscriberAndMessage();

    //     $response = $this->act
    //         ->withHeaders([
    //             'User-Agent' => $this->faker->userAgent(),
    //             'Referer' => 'testing',
    //         ])->json("POST", "/api/subscriber/{$this->subscriber['id']}/message/{$this->messageId}/push");
    //     // $response->dump();

    //     $response
    //         ->assertSuccessful()
    //         ->assertHeader("content-type", "application/json")
    //         ->assertJsonFragment(["response" => "OK"]);
    // }
}
