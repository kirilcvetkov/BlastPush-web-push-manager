<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTriggerScheduleIdToSchedules extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('is_trigger')->nullable()->after('day');
            $table->unsignedBigInteger('trigger_schedule_id')->nullable()->after('is_trigger');
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('is_trigger');
            $table->dropColumn('trigger_schedule_id');
        });
    }
}
