<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public $table = 'tb_check_clock';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->uuid('id_user');
            $table->uuid('id_ck_setting');
            $table->uuid('id_ck_setting_time');
            $table->time('clock_in');
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();
            $table->time('clock_out')->nullable();
            $table->enum('status', ['on-time', 'late'])->default('on-time');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('cascade');
            $table->foreign('id_ck_setting')->references('id')->on('tb_check_clock_setting')->onDelete('cascade');
            $table->foreign('id_ck_setting_time')->references('id')->on('tb_check_clock_setting_time')->onDelete('cascade');
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
