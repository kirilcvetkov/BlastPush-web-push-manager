<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhooksTable extends Migration
{
    public function up()
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->foreignId('event_id')->unsigned()->references('id')->on('events');
            $table->foreignId('website_id')->unsigned()->references('id')->on('websites');
            $table->tinyInteger('event_type_id')->unsigned();
            $table->tinyInteger('tries')->unsigned()->default(0);
            $table->text('request_url')->nullable()->default(null);
            $table->text('request_body')->nullable()->default(null);
            $table->tinyInteger('request_method')->unsigned()->default(0);
            $table->integer('response_status')->nullable()->default(null);
            $table->text('response_headers')->nullable()->default(null);
            $table->text('response_body')->nullable()->default(null);
            $table->tinyInteger('status')->unsigned();
        });
    }

    public function down()
    {
        Schema::dropIfExists('webhooks');
    }
}
