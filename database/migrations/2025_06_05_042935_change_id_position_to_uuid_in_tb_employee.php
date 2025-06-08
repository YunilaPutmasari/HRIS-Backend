<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIdPositionToUuidInTbEmployee extends Migration
{
    public function up()
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            // Hapus foreign key lama (kalau ada)
            $table->dropForeign(['id_position']);

            // Ubah kolom jadi UUID
            $table->uuid('id_position')->nullable()->change();


            // Tambah foreign key baru ke tb_position
            $table->foreign('id_position')->references('id')->on('tb_position');
        });
    }

    public function down()
    {
        Schema::table('tb_employee', function (Blueprint $table) {
            $table->dropForeign(['id_position']);
            $table->integer('id_position')->change();
        });
    }
}
