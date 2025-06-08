<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_employee_request', function (Blueprint $table) {
            $table->dateTime('end_date')->change();
            $table->dateTime('start_date')->change();
            $table->dateTime('approval_date')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_employee_request', function (Blueprint $table) {
            $table->date('end_date')->change();
            $table->date('start_date')->change();
            $table->dropColumn('approval_date');
        });
    }
};
