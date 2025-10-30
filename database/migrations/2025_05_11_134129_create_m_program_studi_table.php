<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMProgramStudiTable extends Migration
{
    public function up()
    {
        Schema::create('m_program_studi', function (Blueprint $table) {
            $table->bigIncrements('prodi_id');
            $table->string('nama_prodi', 100)->unique();
            $table->string('jurusan', 100);
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_program_studi');
    }
}
