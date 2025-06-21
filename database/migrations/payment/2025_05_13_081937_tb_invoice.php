<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public $table = 'tb_invoice';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('public.uuid_generate_v4()'));
            $table->uuid('id_company');
            $table->uuid('id_subscription');
            $table->double('total_amount');
            $table->enum('status', ['paid', 'unpaid', 'failed'])->default('unpaid');
            $table->datetime('due_datetime');
            $table->string('xendit_invoice_id')->nullable();
            $table->string('invoice_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('id_subscription')->references('id')->on('tb_subscription')->onDelete('set null');
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
