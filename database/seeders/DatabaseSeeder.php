<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin (perusahaan) untuk login — role admin = semua akses,
        // termasuk blog dan webapp. Login pakai email atau username "admin".
        // GANTI password ini setelah pertama kali login di lingkungan nyata.
        User::updateOrCreate(
            ['email' => 'admin@funfarm.id'],
            [
                'name' => 'Admin FunFarm',
                'username' => 'admin',
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make('admin123'),
            ]
        );

        // Akun demo Fase 2 (untuk uji RBAC; hapus/ganti di lingkungan nyata).
        // Pemilik member: login username "budi", password "pemilik123".
        User::updateOrCreate(
            ['username' => 'budi'],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@funfarm.local',
                'role' => User::ROLE_PEMILIK,
                'phone' => '081200000001',
                'address' => 'Dusun Krajan, Desa Sukamaju',
                'is_member' => true,
                'password' => Hash::make('pemilik123'),
            ]
        );

        // Pemilik bukan member: login username "sari", password "pemilik123".
        User::updateOrCreate(
            ['username' => 'sari'],
            [
                'name' => 'Sari Wulandari',
                'email' => 'sari@funfarm.local',
                'role' => User::ROLE_PEMILIK,
                'phone' => '081200000002',
                'address' => 'Dusun Tengah, Desa Sukamaju',
                'is_member' => false,
                'password' => Hash::make('pemilik123'),
            ]
        );

        // Penggarap: login username "joko", password "penggarap123".
        User::updateOrCreate(
            ['username' => 'joko'],
            [
                'name' => 'Joko Prasetyo',
                'email' => 'joko@funfarm.local',
                'role' => User::ROLE_PENGGARAP,
                'phone' => '081200000003',
                'address' => 'Dusun Krajan, Desa Sukamaju',
                'is_member' => false,
                'password' => Hash::make('penggarap123'),
            ]
        );

        $this->call([
            ArticleSeeder::class,
        ]);
    }
}
