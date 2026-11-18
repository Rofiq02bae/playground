<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin awal untuk /admin/login.
        // GANTI password ini setelah pertama kali login di lingkungan nyata.
        User::updateOrCreate(
            ['email' => 'admin@funfarm.id'],
            [
                'name' => 'Admin FunFarm',
                'password' => Hash::make('admin123'),
            ]
        );

        $this->call([
            ArticleSeeder::class,
        ]);
    }
}
