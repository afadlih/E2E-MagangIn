<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('t_lowongan_magang', function (Blueprint $table) {
            $table->integer('kuota')->default(1)->after('status')
                  ->comment('Jumlah posisi yang dibuka per lowongan');
        });
    }

    public function down()
    {
        Schema::table('t_lowongan_magang', function (Blueprint $table) {
            $table->dropColumn('kuota');
        });
    }
};
