<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTLowonganMagangTable extends Migration
{
    public function up()
    {
        Schema::create('t_lowongan_magang', function (Blueprint $table) {
            $table->bigIncrements('lowongan_id');
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->datetime('tanggal_mulai_magang')->nullable();
            $table->datetime('deadline_lowongan')->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->unsignedBigInteger('perusahaan_id');
            $table->unsignedBigInteger('periode_id')->nullable();
            $table->string('sylabus_path', 255)->nullable();

            $table->foreign('perusahaan_id')
                  ->references('perusahaan_id')->on('m_perusahaan_mitra');
            $table->foreign('periode_id')
                  ->references('periode_id')->on('m_periode_magang');
        });
    }

    public function down()
    {
        Schema::table('t_lowongan_magang', function (Blueprint $table) {
            $table->dropForeign(['perusahaan_id']);
            $table->dropForeign(['periode_id']);
        });
        Schema::dropIfExists('t_lowongan_magang');
    }
}
