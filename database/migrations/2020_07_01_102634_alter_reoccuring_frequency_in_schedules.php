<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterReoccuringFrequencyInSchedules extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->renameColumn('reoccuring_frequency', 'reoccurring_frequency');
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->renameColumn('reoccurring_frequency', 'reoccuring_frequency');
        });
    }
}
