<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('m_users', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('username', 100)->unique();
            $table->string('password', 255);
            $table->unsignedTinyInteger('level_id');
            $table->foreign('level_id')
                  ->references('level_id')->on('r_auth_level')
                  ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('m_users', function (Blueprint $table) {
            $table->dropForeign(['level_id']);
        });
        Schema::dropIfExists('m_users');
    }
};
