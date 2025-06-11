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
        Schema::table('tb_invoice', function (Blueprint $table) {
            $table->string('xendit_invoice_id')->nullable();
            $table->string('invoice_url')->nullable();
            $table->uuid('id_subscription');
            
            $table->foreign('id_subscription')->references('id')->on('tb_subscription')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_invoice', function (Blueprint $table) {
            //
        });
    }
};
