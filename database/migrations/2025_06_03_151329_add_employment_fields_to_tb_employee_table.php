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
            $table->date('startDate')->nullable()->after('notelp');
            $table->date('endDate')->nullable()->after('startDate');
            $table->string('tenure', 50)->nullable()->after('endDate');
            $table->decimal('gaji', 15, 2)->nullable()->after('tenure');
            $table->decimal('uangLembur', 15, 2)->nullable()->after('gaji');
            $table->decimal('dendaTerlambat', 15, 2)->nullable()->after('uangLembur');
            $table->decimal('TotalGaji', 15, 2)->nullable()->after('dendaTerlambat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            $table->dropColumn([
                'startDate',
                'endDate',
                'tenure',
                'gaji',
                'uangLembur',
                'dendaTerlambat',
                'TotalGaji',
            ]);
        });
    }
};
