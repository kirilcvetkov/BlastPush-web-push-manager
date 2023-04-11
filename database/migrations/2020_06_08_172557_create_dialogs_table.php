<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDialogsTable extends Migration
{
    public function up()
    {
        Schema::create('dialogs', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_global')->default(false);
            $table->string('message', 500)->default('{{ website }} wants to send you notifications');
            $table->string('image', 500)->nullable()->default(null);
            $table->smallInteger('delay')->unsigned()->default(0);
            $table->string('button_allow', 50)->default('ALLOW');
            $table->string('button_block', 50)->default('BLOCK');
            $table->tinyInteger('show_percentage')->unsigned()->default(50);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dialogs');
    }
}
