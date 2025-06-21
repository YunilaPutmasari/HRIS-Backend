<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{   
    public $table = 'tb_daily_usage_records';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_daily_usage_records', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->uuid('id_company');
            $table->uuid('id_subscription');
            $table->date('date');
            $table->float('daily_cost');
            $table->timestamps();

            // Indexes
            $table->index(['id_company', 'date']);

            // Foreign Key
            $table->foreign('id_company')->references('id')->on('tb_company')->onDelete('cascade');
            $table->foreign('id_subscription')->references('id')->on('tb_subscription')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_daily_usage_records');
    }
};
