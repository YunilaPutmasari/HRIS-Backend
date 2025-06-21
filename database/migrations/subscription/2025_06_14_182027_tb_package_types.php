<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $table = 'tb_package_types';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_package_types', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('max_seats'); // maksimal seat/employee
            $table->float('price_per_seat'); // harga per seat/hari atau per bulan
            $table->boolean('is_free')->default(false); // apakah ini free plan?
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_package_types');
    }
};
