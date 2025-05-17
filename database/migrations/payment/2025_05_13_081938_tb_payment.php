<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public $table = 'tb_payment';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->uuid('id_invoice');
            $table->string('payment_code')->unique();
            $table->double('amount_paid');
            $table->string('currency')->default('IDR');
            $table->enum('payment_method', ['credit_card', 'bank_transfer', 'e_wallet'])->default('bank_transfer');
            $table->enum('status', ['success', 'failed'])->default('failed');
            $table->datetime('payment_datetime');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_invoice')->references('id')->on('tb_invoice')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
