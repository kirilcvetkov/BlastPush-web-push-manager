<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduledAtToSchedules extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->nullable()->after('delay');
            $table->enum('reoccuring_frequency', config('constants.campaignReoccurringFrequency'))->nullable()
                ->after('scheduled_at');
            $table->time('hour_minute')->nullable()->after('reoccuring_frequency');
            $table->tinyInteger('day')->nullable()->after('hour_minute');
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
            $table->dropColumn('reoccuring_frequency');
            $table->dropColumn('hour_minute');
            $table->dropColumn('day');
        });
    }
}
