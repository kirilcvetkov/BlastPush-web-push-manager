<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsMobileIsTabletIsDesktopToEventsTable extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_mobile')->after('browser')->default(false);
            $table->boolean('is_tablet')->after('is_mobile')->default(false);
            $table->boolean('is_desktop')->after('is_tablet')->default(false);
            $table->boolean('is_robot')->after('is_desktop')->default(false);
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_mobile');
            $table->dropColumn('is_tablet');
            $table->dropColumn('is_desktop');
            $table->dropColumn('is_robot');
        });
    }
}
