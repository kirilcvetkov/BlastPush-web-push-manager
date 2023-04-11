<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMessageIdInPushes extends Migration
{
    public function up()
    {
        Schema::table('pushes', function (Blueprint $table) {
            $table->foreignId('message_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pushes', function (Blueprint $table) {
            //
        });
    }
}
