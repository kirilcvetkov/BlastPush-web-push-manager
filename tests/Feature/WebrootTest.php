<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebrootTest extends TestCase
{
    public function testLogin()
    {
        $response = $this->get('/login');
        $response->assertStatus(200)->assertSee("BlastPush");
    }
}
