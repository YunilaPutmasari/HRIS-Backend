<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnsToSnakecaseInKaryawansTable extends Migration
{
    public function up()
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            $table->renameColumn('jenisKelamin', 'jenis_kelamin');
            $table->renameColumn('tipeKontrak', 'tipe_kontrak');
            $table->renameColumn('tempatLahir', 'tempat_lahir');
            $table->renameColumn('tanggalLahir', 'tanggal_lahir');
            $table->renameColumn('startDate', 'start_date');
            $table->renameColumn('endDate', 'end_date');
            $table->renameColumn('uangLembur', 'uang_lembur');
            $table->renameColumn('dendaTerlambat', 'denda_terlambat');
            $table->renameColumn('TotalGaji', 'total_gaji');
            $table->renameColumn('tanggalEfektif', 'tanggal_efektif');
            $table->renameColumn('notelp', 'no_telp');
        });
    }

    public function down()
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            $table->renameColumn('jenis_kelamin', 'jenisKelamin');
            $table->renameColumn('tipe_kontrak', 'tipeKontrak');
            $table->renameColumn('tempat_lahir', 'tempatLahir');
            $table->renameColumn('tanggal_lahir', 'tanggalLahir');
            $table->renameColumn('start_date', 'startDate');
            $table->renameColumn('end_date', 'endDate');
            $table->renameColumn('uang_lembur', 'uangLembur');
            $table->renameColumn('denda_terlambat', 'dendaTerlambat');
            $table->renameColumn('total_gaji', 'TotalGaji');
            $table->renameColumn('tanggal_efektif', 'tanggalEfektif');
            $table->renameColumn('no_telp', 'notelp');
        });
    }
}
