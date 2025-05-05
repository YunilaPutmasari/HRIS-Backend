<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


    public $table = "tb_company";

    /**
     * Run the migrations.
     */
    public function up(): void
    {


        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->string("name")->unique();
            $table->uuid("id_manager");
            $table->uuid("id_subscription")->nullable();
            $table->date("effective_date")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("tb_company");
    }
};
