<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public $table = 'tb_employee';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->string('sign_in_code', 6)->default('');
            $table->uuid('id_user');
            $table->enum('employment_status', ['active', 'inactive', 'resign'])->default('active');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address');
            $table->uuid('id_position')->nullable();
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
        Schema::dropIfExists('tb_employee');
    }
};
