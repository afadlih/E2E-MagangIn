<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTLogAktivitasMhsTable extends Migration
{
    public function up()
    {
        Schema::create('t_log_aktivitas_mhs', function (Blueprint $table) {
            $table->bigIncrements('aktivitas_id');
            $table->unsignedBigInteger('lamaran_id');
            $table->text('keterangan');
            $table->timestamp('waktu')->useCurrent();

            $table->foreign('lamaran_id')
                  ->references('lamaran_id')->on('t_lamaran_magang');
        });
    }

    public function down()
    {
        Schema::table('t_log_aktivitas_mhs', function (Blueprint $table) {
            $table->dropForeign(['lamaran_id']);
        });
        Schema::dropIfExists('t_log_aktivitas_mhs');
    }
}
