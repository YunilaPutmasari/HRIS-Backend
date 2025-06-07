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
        Schema::table('tb_user', function (Blueprint $table) {
            $table->boolean('is_admin_new')->default(false);

            \DB::statement("UPDATE tb_user SET is_admin_new = CASE WHEN is_admin = '1' THEN TRUE ELSE FALSE END");

            $table->dropColumn('is_admin');

            $table->boolean('is_admin')->default(false)->after('phone_number');

            \DB::statement("UPDATE tb_user SET is_admin = is_admin_new");

            $table->dropColumn('is_admin_new');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            //
            $table->string('is_admin_old', 255)->default('0');

            // Salin data boolean ke string
            \DB::statement("UPDATE tb_user SET is_admin_old = CASE WHEN is_admin THEN '1' ELSE '0' END");

            // Hapus kolom boolean
            $table->dropColumn('is_admin');

            // Ganti dengan varchar
            $table->string('is_admin', 255)->default('0');

            // Salin balik data
            \DB::statement("UPDATE tb_user SET is_admin = is_admin_old");

            // Hapus kolom sementara
            $table->dropColumn('is_admin_old');
        });
    }
};
