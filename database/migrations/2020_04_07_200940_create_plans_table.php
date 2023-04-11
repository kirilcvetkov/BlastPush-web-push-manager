<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('details');
            $table->integer('website_limit')->default(1);
            $table->integer('message_limit')->default(10);
            $table->integer('subscriber_limit')->default(1000);
            $table->integer('push_limit')->default(10);
            $table->enum('push_limit_timeframe', config('constants.plansPushLimitTimeframe'))->default('unlimited');
            $table->decimal('cost', 8, 2)->default(0.00);
            $table->boolean('can_renew')->default(true);
            $table->boolean('available')->default(false);
            $table->string('color', 20)->default('success');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
