<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterScopeInVariables extends Migration
{
    public function up()
    {
        Schema::table('variables', function (Blueprint $table) {
            DB::statement("ALTER TABLE variables MODIFY COLUMN scope ENUM('global', 'website', 'campaign', 'schedule', 'subscriber')");
        });
    }

    public function down()
    {
        Schema::table('variables', function (Blueprint $table) {
            DB::statement("ALTER TABLE variables MODIFY COLUMN scope ENUM('global', 'website', 'campaign', 'schedule')");
        });
    }
}
