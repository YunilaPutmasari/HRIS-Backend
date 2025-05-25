<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tb_payment', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        Schema::table('tb_payment', function (Blueprint $table) {
            $table->string('payment_method')->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('tb_payment', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        Schema::table('tb_payment', function (Blueprint $table) {
            $table->enum('payment_method', ['credit_card', 'bank_transfer', 'e_wallet'])->default('bank_transfer');
        });
    }
};

