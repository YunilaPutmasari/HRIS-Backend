<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_subscription', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->uuid('id_company');
            $table->uuid('id_package_type')->nullable();
            $table->integer('seats')->default(1);
            $table->boolean('is_trial')->default(false);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->enum('status', ['trial', 'active', 'expired', 'pending_upgrade','pending_downgrade','canceled'])->default('active');
            $table->boolean('is_canceled')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_company')->references('id')->on('tb_company')->onDelete('cascade');
            $table->foreign('id_package_type')->references('id')->on('tb_package_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_subscription');
    }
};

