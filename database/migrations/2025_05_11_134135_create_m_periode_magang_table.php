<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMPeriodeMagangTable extends Migration
{
    public function up()
    {
        Schema::create('m_periode_magang', function (Blueprint $table) {
            $table->bigIncrements('periode_id');
            $table->string('periode');
            $table->string('keterangan', 255)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_periode_magang');
    }
}
