<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->boolean('enabled')->default(true);
            $table->enum('type', config('constants.campaignTypes'));
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('campaign_website', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->foreignId('website_id')->references('id')->on('websites')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['campaign_id', 'website_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaign_website');
        Schema::dropIfExists('campaigns');
    }
}
