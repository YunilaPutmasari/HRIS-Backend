<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            // Tambahkan kolom id_department bertipe UUID, nullable kalau perlu
            $table->uuid('id_department')->nullable()->after('id_position');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            //
        });
    }
};
