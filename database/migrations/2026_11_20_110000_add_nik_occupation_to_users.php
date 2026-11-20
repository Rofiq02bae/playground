<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * NIK & pekerjaan pemilik untuk surat perjanjian.
     * Keduanya OPSIONAL: di surat, barisnya hanya ditampilkan bila terisi.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->after('phone');
            $table->string('occupation')->nullable()->after('nik');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik', 'occupation']);
        });
    }
};
