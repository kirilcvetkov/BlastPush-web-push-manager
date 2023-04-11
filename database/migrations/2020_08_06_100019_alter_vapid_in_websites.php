<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVapidInWebsites extends Migration
{
    public function up()
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->text('vapid_public')->nullable()->change();
            $table->text('vapid_private')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('websites', function (Blueprint $table) {
            //
        });
    }
}
