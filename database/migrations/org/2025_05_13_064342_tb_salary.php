<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public $table = 'tb_salary';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->uuid('id_user');
            $table->enum('type', ['daily', 'weekly', 'monthly', 'contract'])->default('monthly');
            $table->smallInteger('contract_months');
            $table->float('rate');
            $table->date('effective_date');
            $table->uuid('id_position');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('cascade');
            $table->foreign('id_position')->references('id')->on('tb_position')->onDelete('cascade');
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
