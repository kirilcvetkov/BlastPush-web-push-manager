<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\RefreshDatabase;

final class MessageTest extends TestCase
{
    use WithFaker;

    public function testMessageCreate()
    {
        $post = factory(\App\Message::class)->make()->toArray();

        // Unauthorized
        $response = $this->postJson("/api/message", $post);

        $response
            ->assertUnauthorized()
            ->assertHeader("content-type", "application/json");

        // Authorized
        $response = $this->act()->postJson("/api/message", $post);

        $response
            ->assertSuccessful()
            ->assertHeader("content-type", "application/json")
            ->assertJsonFragment(["title" => $post['title']]);
    }

    public function testMessageUpdate()
    {
        $message = \App\Message::where('user_id', '=', $this->user()->id)->get()->last()->toArray();
        $message["title"] = $this->faker->sentence(4);

        // Unauthorized
        $response = $this->putJson("/api/message/{$message['id']}", $message);

        $response
            ->assertUnauthorized()
            ->assertHeader("content-type", "application/json");

        // Authorized
        $response = $this->act()->putJson("/api/message/{$message['id']}", $message);

        $response
            ->assertSuccessful()
            ->assertHeader("content-type", "application/json")
            ->assertJsonFragment(["title" => $message["title"]]);

        // Someone Else's
        $message = $this->someoneElsesMessage();
        $response = $this->act()->putJson("/api/message/{$message['id']}", $message);

        $response
            ->assertNotFound()
            ->assertHeader("content-type", "application/json");
    }

    public function testMessageDelete()
    {
        $message = \App\Message::where('user_id', '=', $this->user()->id)->get()->last()->toArray();

        // Unauthorized
        $response = $this->deleteJson("/api/message/" . $message['id'], $message);

        $response
            ->assertUnauthorized()
            ->assertHeader("content-type", "application/json");

        // Authorized
        $response = $this->act()->deleteJson("/api/message/" . $message['id'], $message);

        $response
            ->assertSuccessful()
            ->assertHeader("content-type", "application/json")
            ->assertJsonFragment(["title" => $message['title']]);

        // Someone Else's
        $message = $this->someoneElsesMessage();
        $response = $this->act()->deleteJson("/api/message/{$message['id']}", $message);

        $response
            ->assertNotFound()
            ->assertHeader("content-type", "application/json");
    }

    public function testMessageIndex()
    {
        // Unauthorized
        $response = $this->getJson("/api/message");

        $response
            ->assertUnauthorized()
            ->assertHeader("content-type", "application/json");

        // Authorized
        $response = $this->act()->getJson("/api/message");

        $response
            ->assertSuccessful()
            ->assertHeader("content-type", "application/json")
            ->assertJsonStructure(['data' => [
                '*' => [
                    "id",
                    "title",
                    "body",
                    "url",
                    "button",
                    "icon",
                    "image",
                    "badge",
                    "sound",
                    "direction",
                    "actions",
                    "silent",
                    "tag",
                    "renotify",
                    "user_id",
                    "created_at",
                    "updated_at",
                    "deleted_at",
                ]
            ]]);
    }
}
