<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUseragentColsGeoipInEvents extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('country', 2)->nullable()->after('ip');
            $table->string('state', 10)->nullable()->after('country');
            $table->string('city', 50)->nullable()->after('state');
            $table->string('postal', 10)->nullable()->after('city');
            $table->string('timezone', 50)->nullable()->after('postal');
            $table->string('device_version', 100)->nullable()->after('device');
            $table->string('platform_version', 20)->nullable()->after('platform');
            $table->string('browser_version', 20)->nullable()->after('browser');
            $table->string('browser_version_short', 10)->nullable()->after('browser_version');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('country');
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->dropColumn('postal');
            $table->dropColumn('timezone');
            $table->dropColumn('device_version');
            $table->dropColumn('platform_version');
            $table->dropColumn('browser_version');
            $table->dropColumn('browser_version_short');
        });
    }
}
