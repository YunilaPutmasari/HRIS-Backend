<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            $table->string('nik')->after('last_name')->nullable()->unique();
            $table->string('email')->after('nik')->nullable()->unique();
            // kamu bisa sesuaikan nullable atau tidak, dan posisi kolom dengan 'after'
        });
    }

    public function down()
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            $table->dropColumn(['nik', 'email']);
        });
    }
};
