<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('type_id');
            $table->string('type', 100);
            $table->uuid('uuid')->index()->references('uuid')->on('pushes')->nullable();
            $table->string('url', 500)->nullable();
            $table->string('location', 1000)->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('device', 200)->nullable();
            $table->string('platform', 200)->nullable();
            $table->string('browser', 300)->nullable();
            $table->string('referer', 500)->nullable();
            $table->text('body')->nullable();
            $table->foreignId('website_id')->unsigned()->references('id')->on('websites')->onDelete('cascade');
            $table->foreignId('subscriber_id')->unsigned()->references('id')->on('subscribers')->onDelete('cascade');
            $table->foreignId('user_id')->unsigned()->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}
