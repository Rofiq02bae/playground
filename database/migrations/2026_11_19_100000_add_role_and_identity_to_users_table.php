<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fondasi RBAC Fase 2: satu tabel users untuk blog + webapp.
     * Role: admin (perusahaan, semua akses), pemilik (view only),
     * penggarap (view + add data, butuh approval perusahaan).
     * Identitas pemilik (username/telepon/alamat) dipakai admin untuk
     * menetapkan status membership (pilihan: membership / bukan membership).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('pemilik')->index();
            $table->string('username')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_member')->default(false);
            $table->string('email')->nullable()->change();
        });

        // Akun admin blog yang sudah ada naik jadi role admin (perusahaan).
        DB::table('users')->where('email', 'admin@funfarm.id')->update([
            'role' => 'admin',
            'username' => 'admin',
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'username', 'phone', 'address', 'is_member']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
