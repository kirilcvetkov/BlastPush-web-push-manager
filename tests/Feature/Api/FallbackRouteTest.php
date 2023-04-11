<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class FallbackRouteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testNonExistingRoute()
    {
        $response = $this->json('GET', '/api/nonexistingroute');
        $response->assertStatus(404);
    }
}
