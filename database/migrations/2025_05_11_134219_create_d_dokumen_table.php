<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDDokumenTable extends Migration
{
    public function up()
    {
        Schema::create('d_dokumen', function (Blueprint $table) {
            $table->bigIncrements('dokumen_id');
            $table->unsignedBigInteger('lamaran_id');
            $table->string('jenis', 50)->nullable();
            $table->string('nama_file', 255);
            $table->bigInteger('ukuran')->nullable();
            $table->string('path', 255);
            $table->timestamp('uploaded_at')->useCurrent();

            $table->foreign('lamaran_id')
                  ->references('lamaran_id')->on('t_lamaran_magang');
        });
    }

    public function down()
    {
        Schema::table('d_dokumen', function (Blueprint $table) {
            $table->dropForeign(['lamaran_id']);
        });
        Schema::dropIfExists('d_dokumen');
    }
}
