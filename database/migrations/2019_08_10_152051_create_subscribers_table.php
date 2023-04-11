<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('subscribed')->default(1);
            $table->string('endpoint', 1000);
            $table->string('hash', 32);
            $table->unsignedBigInteger('expiration')->nullable();
            $table->string('public', 255)->nullable();
            $table->string('auth', 255)->nullable();
            $table->string('encoding', 50)->nullable();
            $table->text('body')->nullable();
            $table->foreignId('website_id')->unsigned()->references('id')->on('websites')->onDelete('cascade');
            $table->foreignId('user_id')->unsigned()->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['website_id', 'hash']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscribers');
    }
}
