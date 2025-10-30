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
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            $table->string('profile_picture')->nullable()->after('telp');
        });
    }

    public function down()
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            $table->dropColumn('profile_picture');
        });
    }

};
