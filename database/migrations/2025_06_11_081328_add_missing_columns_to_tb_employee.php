<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            // Kolom yang hilang
            $table->uuid('id_department');
            $table->string('nik')->nullable();
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

            $table->foreign('id_department')->references('id')->on('tb_department')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            $table->dropColumn([
                'id_department',
                'employment_status',
                'address',
                'jenis_kelamin',
                'no_telp',
                'cabang',
                'grade',
                'bank',
                'no_rek',
                'pendidikan',
                'tipe_kontrak',
                'tempat_lahir',
                'tanggal_lahir',
                'dokumen',
                'avatar',
                'start_date',
                'end_date',
                'tanggal_efektif',
            ]);
        });
    }
};