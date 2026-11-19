<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Impor data wilayah dari database/seeders/data/wilayah.csv
     * (konversi dari cahyadsn/wilayah db/wilayah.sql — 91.599 baris:
     * 38 provinsi, 514 kabupaten/kota, 7.285 kecamatan, 83.762 desa).
     * Level & kode induk diturunkan dari struktur kode bertitik.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/wilayah.csv');

        if (! file_exists($path)) {
            $this->command?->warn('File wilayah.csv tidak ditemukan, RegionSeeder dilewati.');

            return;
        }

        DB::transaction(function () use ($path) {
            DB::table('regions')->delete();

            $handle = fopen($path, 'r');
            fgetcsv($handle); // lewati header

            $batch = [];
            while (($row = fgetcsv($handle)) !== false) {
                [$kode, $nama] = $row;
                $pos = strrpos($kode, '.');

                $batch[] = [
                    'kode' => $kode,
                    'nama' => $nama,
                    'level' => substr_count($kode, '.') + 1,
                    'parent_kode' => $pos === false ? null : substr($kode, 0, $pos),
                ];

                if (count($batch) >= 1000) {
                    DB::table('regions')->insert($batch);
                    $batch = [];
                }
            }

            if ($batch) {
                DB::table('regions')->insert($batch);
            }

            fclose($handle);
        });
    }
}
