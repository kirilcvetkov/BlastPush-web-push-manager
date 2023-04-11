<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePushesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pushes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->foreignId('message_id')->references('id')->on('messages')->onDelete('cascade');
            $table->foreignId('website_id')->references('id')->on('websites')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('uuid')->index();
            $table->integer('is_success')->default(1);
            $table->text('response')->nullable();
            $table->integer('http_code')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pushes');
    }
}
