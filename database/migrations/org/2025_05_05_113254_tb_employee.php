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
            $table->string('first_name');
            $table->string('last_name');
            $table->string('nik')->nullable()->unique();
            $table->enum('employment_status', ['active', 'inactive', 'resign'])->default('active');
            $table->string('address')->nullable();
            $table->uuid('id_position')->nullable();
            // $table->uuid('id_jadwal')->nullable();
            // =======================
            $table->string('jenis_kelamin')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('cabang')->nullable();
            $table->string('grade')->nullable();
            $table->string('bank')->nullable();
            $table->string('no_rek')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('tipe_kontrak')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('dokumen')->nullable();
            $table->string('avatar')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('tanggal_efektif')->nullable();
            
            $table->timestamps();
            $table->softDeletes();




            $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('cascade');
            $table->foreign('id_position')->references('id')->on('tb_position')->onDelete('cascade');
            // $table->foreign('id_jadwal')->references('id')->on('tb_position')->onDelete('cascade');

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
