<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMPerusahaanMitraTable extends Migration
{
    public function up()
    {
        Schema::create('m_perusahaan_mitra', function (Blueprint $table) {
            $table->bigIncrements('perusahaan_id');
            $table->string('nama', 100);
            $table->text('alamat');
            $table->string('email', 100)->nullable();
            $table->string('telp', 20)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_perusahaan_mitra');
    }
}
