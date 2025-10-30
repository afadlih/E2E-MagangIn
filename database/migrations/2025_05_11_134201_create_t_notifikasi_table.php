<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTNotifikasiTable extends Migration
{
    public function up()
    {
        Schema::create('t_notifikasi', function (Blueprint $table) {
            $table->bigIncrements('notifikasi_id');
            $table->unsignedBigInteger('penerima_id');
            $table->string('judul', 100)->nullable();
            $table->text('pesan')->nullable();
            $table->timestamp('waktu_dibuat')->useCurrent();
            $table->boolean('status_baca')->default(false);
            $table->string('tipe', 50)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_notifikasi');
    }
}
