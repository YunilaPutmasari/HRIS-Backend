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
        Schema::table('tb_company', function (Blueprint $table) {
            $table->boolean('has_used_trial')->default(false)->after('id_subscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_company', function (Blueprint $table) {
            $table->dropColumn('has_used_trial');
        });
    }
};
