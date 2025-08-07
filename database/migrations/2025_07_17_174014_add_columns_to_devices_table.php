<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            // Tambahkan kolom-kolom yang hilang
            $table->integer('port')->default(8728)->after('ip_address'); // default 8728
            $table->string('username')->default('admin')->after('port'); // default admin
            $table->string('password')->nullable()->after('username'); // nullable
            $table->string('description')->nullable()->after('password'); // nullable
            // Jika ip_address belum unique, tambahkan ini
            $table->unique('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            // Hapus kolom-kolom jika rollback
            $table->dropColumn(['port', 'username', 'password', 'description']);
            // Jika ip_address diubah menjadi unique, hapus index unique-nya juga
            $table->dropUnique(['ip_address']);
        });
    }
};