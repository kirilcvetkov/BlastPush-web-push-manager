<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampaignIdScheduleIdToPushesTable extends Migration
{
    public function up()
    {
        Schema::table('pushes', function (Blueprint $table) {
            $table->foreignId('campaign_id')->references('id')->on('campaigns')->nullable()->after('message_id');
            $table->foreignId('schedule_id')->references('id')->on('schedules')->nullable()->after('campaign_id');
        });
    }

    public function down()
    {
        Schema::table('pushes', function (Blueprint $table) {
            $table->dropForeign('pushes_campaign_id_foreign');
            $table->dropForeign('pushes_schedule_id_foreign');
            $table->dropColumn('schedule_id');
            $table->dropColumn('campaign_id');
        });
    }
}
