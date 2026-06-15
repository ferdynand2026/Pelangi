<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah enum role users: tambah 'dinas'
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','dinas','tpi','pembeli') NOT NULL DEFAULT 'pembeli'");

        // 2. Tambah dinas_id di users (untuk TPI: siapa dinas yang membuatnya)
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('dinas_id')->nullable()->after('role');
            $table->foreign('dinas_id')->references('id')->on('users')->onDelete('set null');
        });

        // 3. Tambah tpi_id di produks
        Schema::table('produks', function (Blueprint $table) {
            $table->unsignedBigInteger('tpi_id')->nullable()->after('id');
            $table->foreign('tpi_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 4. Tambah tpi_id di jadwal
        Schema::table('jadwal', function (Blueprint $table) {
            $table->unsignedBigInteger('tpi_id')->nullable()->after('id');
            $table->foreign('tpi_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropForeign(['tpi_id']);
            $table->dropColumn('tpi_id');
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->dropForeign(['tpi_id']);
            $table->dropColumn('tpi_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['dinas_id']);
            $table->dropColumn('dinas_id');
        });

        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','tpi','pembeli') NOT NULL DEFAULT 'pembeli'");
    }
};