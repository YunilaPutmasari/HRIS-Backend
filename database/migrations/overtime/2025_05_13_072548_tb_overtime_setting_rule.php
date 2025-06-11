<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public $table = 'tb_overtime_setting_rule';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->uuid('id_overtime_setting');
            $table->enum('day_type', ['weekday', 'weekend', 'holiday'])->default('weekday');
            $table->time('start_hour');
            $table->time('end_hour');
            $table->float('rate_multiplier');
            $table->smallInteger('max_hour')->default(0); // 0 = unlimited
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_overtime_setting')->references('id')->on('tb_overtime_setting')->onDelete('cascade');
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
