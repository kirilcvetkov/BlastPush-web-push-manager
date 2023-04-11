<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Push;

class PushSeeder extends Seeder
{
    public function run()
    {
        Push::factory()->count(50)->create();
    }
}
