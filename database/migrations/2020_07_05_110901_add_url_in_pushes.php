<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlInPushes extends Migration
{
    public function up()
    {
        Schema::table('pushes', function (Blueprint $table) {
            $table->string('url', 2000)->nullable()->after('uuid');
        });
    }

    public function down()
    {
        Schema::table('pushes', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
}
