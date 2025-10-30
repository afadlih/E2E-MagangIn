<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDosenTable extends Migration
{
    public function up()
    {
        Schema::create('m_dosen', function (Blueprint $table) {
            $table->bigIncrements('dosen_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nama', 100);
            $table->string('email', 100);
            $table->string('telp', 20)->nullable();

            $table->foreign('user_id')
                  ->references('user_id')->on('m_users')
                  ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::table('m_dosen', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('m_dosen');
    }
}
