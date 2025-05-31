<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public $table = 'tb_employee';

    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->string('jenisKelamin')->nullable()->after('last_name');
            $table->string('notelp')->nullable()->after('jenisKelamin');
            $table->string('cabang')->nullable()->after('notelp');
            $table->string('jabatan')->nullable()->after('cabang');
            $table->string('grade')->nullable();
            $table->string('bank')->nullable();
            $table->string('norek')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('jadwal')->nullable();
            $table->string('tipeKontrak')->nullable();
            $table->string('tempatLahir')->nullable();
            $table->date('tanggalLahir')->nullable();
            $table->string('dokumen')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn([
                'jenisKelamin',
                'notelp',
                'cabang',
                'jabatan',
                'grade',
                'bank',
                'norek',
                'pendidikan',
                'jadwal',
                'tipeKontrak',
                'tempatLahir',
                'tanggalLahir',
                'dokumen',
            ]);
        });
    }
};
