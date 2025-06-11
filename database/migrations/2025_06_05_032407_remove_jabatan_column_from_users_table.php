<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (Schema::hasColumn('tb_employee', 'jabatan')) {
            Schema::table('tb_employee', function (Blueprint $table) {
                $table->dropColumn('jabatan');
            });
        }
    }

    public function down()
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            $table->string('jabatan')->nullable();
        });
    }

};
