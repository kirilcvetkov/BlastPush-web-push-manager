<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduledToSendAtSentAtToPushes extends Migration
{
    public function up()
    {
        Schema::table('pushes', function (Blueprint $table) {
            $table->timestamp('scheduled_to_send_at')->nullable();
            $table->timestamp('sent_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pushes', function (Blueprint $table) {
            $table->dropColumn('scheduled_to_send_at');
            $table->dropColumn('sent_at');
        });
    }
}
