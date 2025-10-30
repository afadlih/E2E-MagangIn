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
            $table->enum('status', ['aktif','nonaktif'])
                ->default('aktif')
                ->after('sylabus_path');
        });
    }

    public function down()
    {
        Schema::table('t_lowongan_magang', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

};
