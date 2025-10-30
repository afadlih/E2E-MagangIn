<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMMahasiswaTable extends Migration
{
    public function up()
    {
        Schema::create('m_mahasiswa', function (Blueprint $table) {
            $table->string('mhs_nim', 20)->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('full_name', 100);
            $table->text('alamat')->nullable();
            $table->string('telp', 20)->nullable();
            $table->unsignedBigInteger('prodi_id');
            $table->unsignedBigInteger('bidang_keahlian_id')->nullable();
            $table->enum('status_magang', ['Belum Magang','Sedang Magang','Selesai Magang'])
                  ->default('Belum Magang');

            $table->unique('user_id');
            // foreign key
            $table->foreign('user_id')
                  ->references('user_id')->on('m_users')
                  ->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('prodi_id')
                  ->references('prodi_id')->on('m_program_studi');
            $table->foreign('bidang_keahlian_id')
                  ->references('id')->on('m_bidang_keahlian');
        });
    }

    public function down()
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['prodi_id']);
        });
        Schema::dropIfExists('m_mahasiswa');
    }
}
