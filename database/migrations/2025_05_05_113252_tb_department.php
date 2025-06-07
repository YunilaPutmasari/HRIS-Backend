<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public $table = 'tb_department';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->uuid('id_company');
            $table->string('name');
            $table->string('location');
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
        //
    }
};
