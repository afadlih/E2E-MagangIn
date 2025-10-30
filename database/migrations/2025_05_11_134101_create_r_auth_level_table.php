<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRAuthLevelTable extends Migration
{
    public function up()
    {
        Schema::create('r_auth_level', function (Blueprint $table) {
            $table->tinyIncrements('level_id');
            $table->string('level_name', 50);
            $table->text('description')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('r_auth_level');
    }
}
