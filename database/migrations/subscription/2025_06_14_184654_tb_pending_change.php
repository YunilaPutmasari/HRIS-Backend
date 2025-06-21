<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $table = 'tb_pending_change';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_pending_change', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->uuid('id_subscription');
            $table->uuid('id_new_package_type')->nullable(); // bisa null jika cuma ganti seats
            $table->integer('new_seats');
            $table->enum('change_type', ['upgrade', 'downgrade']);
            $table->string('status')->default('pending'); // pending/approved/rejected/cancelled
            $table->text('reason_rejected')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_subscription')->references('id')->on('tb_subscription')->onDelete('cascade');
            $table->foreign('id_new_package_type')->references('id')->on('tb_package_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pending_change');
    }
};
