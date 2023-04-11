<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 500);
            $table->string('title', 500);
            $table->string('body', 500);
            $table->string('url', 500);
            $table->string('button', 50);
            $table->string('icon', 500)->nullable();
            $table->string('image', 500)->nullable();
            $table->string('badge', 500)->nullable();
            $table->string('sound', 500)->nullable();
            $table->enum('direction', config('constants.direction'))->default(current(config('constants.direction')));
            $table->text('actions')->nullable();
            $table->boolean('silent')->default(false);
            $table->string('tag', 100)->nullable();
            $table->boolean('renotify')->default(false);
            $table->integer('ttl')->unsigned()->nullable();
            $table->foreignId('user_id')->unsigned()->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
