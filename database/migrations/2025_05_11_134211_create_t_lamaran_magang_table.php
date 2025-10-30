<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTLamaranMagangTable extends Migration
{
    public function up()
    {
        Schema::create('t_lamaran_magang', function (Blueprint $table) {
            $table->bigIncrements('lamaran_id');
            $table->string('mhs_nim', 20);
            $table->unsignedBigInteger('lowongan_id');
            $table->timestamp('tanggal_lamaran')->useCurrent();
            $table->enum('status', ['pending','diterima','ditolak'])
                  ->default('pending');
            $table->foreign('mhs_nim')
                  ->references('mhs_nim')->on('m_mahasiswa');
            $table->foreign('lowongan_id')
                  ->references('lowongan_id')->on('t_lowongan_magang');
        });
    }

    public function down()
    {
        Schema::table('t_lamaran_magang', function (Blueprint $table) {
            $table->dropForeign(['mhs_nim']);
            $table->dropForeign(['lowongan_id']);
        });
        Schema::dropIfExists('t_lamaran_magang');
    }
}
