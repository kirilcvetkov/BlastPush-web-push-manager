<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTypeEnumInCampaigns extends Migration
{
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            DB::statement("
                ALTER TABLE `campaigns` CHANGE `type`
                    `type` ENUM('" . join("','", config('constants.campaignTypes')) . "') NOT NULL
            ");
        });
    }

    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            //
        });
    }
}
