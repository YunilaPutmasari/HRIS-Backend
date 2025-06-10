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
            $table->uuid('id_user');
            $table->double('total_amount');
            $table->datetime('due_datetime');
            $table->enum('status', ['paid', 'unpaid', 'failed'])->default('unpaid');
            $table->string('xendit_invoice_id')->nullable();
            $table->string('invoice_url')->nullable();
            $table->uuid('id_subscription');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('id_subscription')->references('id')->on('tb_subscription')->onDelete('set null');
            $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('cascade');
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
