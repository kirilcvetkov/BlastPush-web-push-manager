<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsitesTable extends Migration
{
    public function up()
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('domain', 100);
            $table->text('vapid_public');
            $table->text('vapid_private');
            $table->string('webhook_url', 1000)->nullable();
            $table->tinyInteger('webhook_method')->unsigned()->default(0);
            $table->set('webhook_event_types', array_keys(config('constants.eventTypes')))->nullable();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['domain', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('websites');
    }
}
