<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDedupeInWebsites extends Migration
{
    public function up()
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->boolean('dedupe_subscribers')->default(false)->after('image');
        });
    }

    public function down()
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn('dedupe_subscribers');
        });
    }
}
