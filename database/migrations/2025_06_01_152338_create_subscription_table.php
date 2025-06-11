<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_subscription', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_company');
            $table->enum('package_type', ['free', 'standard', 'premium']);
            $table->integer('seats')->default(1);
            $table->float('price_per_seat')->nullable(); // free = null
            $table->boolean('is_trial')->default(false);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->enum('status', ['trial', 'active', 'expired'])->default('trial');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_company')->references('id')->on('tb_company')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_subscription');
    }
};

