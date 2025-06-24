<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public $table = 'tb_check_clock_setting';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->uuid('id_company');
            $table->string('name');
            $table->enum('type', ['WFA', 'WFO', 'Hybrid'])->default('WFO');
            $table->double('location_lat')->nullable();
            $table->double('location_lng')->nullable();
            $table->integer('radius')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_company')->references('id')->on('tb_company')->onDelete('cascade');
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
