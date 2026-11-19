<?php

namespace Database\Seeders;

use App\Models\AgriculturalProject;
use App\Models\ProjectExpense;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class Fase2Seeder extends Seeder
{
    public function run(): void
    {
        // Master layanan & harga (dummy, bisa diubah admin di menu Layanan & Harga).
        $drone = Service::updateOrCreate(
            ['slug' => 'drone-semprot'],
            ['name' => 'Drone Semprot', 'branch' => 'pertanian', 'unit' => 'ha',
             'price_normal' => 300000, 'price_member' => 250000,
             'description' => 'Penyemprotan pupuk cair / pestisida dengan drone.', 'is_active' => true]
        );

        $mesinTanam = Service::updateOrCreate(
            ['slug' => 'mesin-tanam'],
            ['name' => 'Mesin Tanam', 'branch' => 'pertanian', 'unit' => 'ha',
             'price_normal' => 400000, 'price_member' => 350000,
             'description' => 'Mesin tanam padi (rice transplanter).', 'is_active' => true]
        );

        Service::updateOrCreate(
            ['slug' => 'mesin-penetas-telur'],
            ['name' => 'Mesin Penetas Telur', 'branch' => 'peternakan', 'unit' => 'hari',
             'price_normal' => 60000, 'price_member' => 50000,
             'description' => 'Mesin penetas telur untuk peternakan.', 'is_active' => true]
        );

        $admin = User::where('username', 'admin')->first();
        $budi = User::where('username', 'budi')->first();
        $joko = User::where('username', 'joko')->first();

        if (! $admin || ! $budi || ! $joko) {
            return;
        }

        // Proyek demo: sawah Budi (member), digarap Joko.
        $project = AgriculturalProject::updateOrCreate(
            ['name' => 'Musim Tanam 1 - Sawah Budi'],
            [
                'owner_id' => $budi->id,
                'cultivator_id' => $joko->id,
                'field_name' => 'Sawah Blok A',
                'location' => 'Desa Sukamaju',
                'area_size' => 2.00,
                'coordinates' => null,
                'crop_type' => 'Padi',
                'crop_variety' => 'Inpari 32',
                'start_date' => '2026-11-01',
                'estimated_harvest_date' => '2027-02-15',
                'status' => 'berjalan',
                'profit_share_company_percent' => 50,
                'created_by' => $admin->id,
            ]
        );

        // Pencatatan demo yang sudah disetujui — totalnya Rp3.400.000,
        // sama dengan contoh rekapitulasi di README (Bagian B.4).
        $approved = [
            ['service_id' => $drone->id, 'category' => 'sewa_drone', 'name' => 'Penyemprotan drone', 'item_type' => 'Penyemprotan pestisida', 'quantity' => 2, 'unit' => 'ha', 'unit_price' => 250000, 'total_price' => 500000, 'date' => '2026-11-10'],
            ['service_id' => $mesinTanam->id, 'category' => 'sewa_mesin_tanam', 'name' => 'Tanam dengan mesin tanam', 'item_type' => null, 'quantity' => 2, 'unit' => 'ha', 'unit_price' => 350000, 'total_price' => 700000, 'date' => '2026-11-05'],
            ['service_id' => null, 'category' => 'pupuk', 'name' => 'Pupuk Urea', 'item_type' => 'Urea', 'quantity' => 10, 'unit' => 'sak', 'unit_price' => 140000, 'total_price' => 1400000, 'date' => '2026-11-12'],
            ['service_id' => null, 'category' => 'obat', 'name' => 'Pestisida', 'item_type' => 'Insektisida', 'quantity' => 3, 'unit' => 'botol', 'unit_price' => 100000, 'total_price' => 300000, 'date' => '2026-11-15'],
            ['service_id' => null, 'category' => 'tenaga_kerja', 'name' => 'Upah tenaga kerja', 'item_type' => null, 'quantity' => 5, 'unit' => 'HOK', 'unit_price' => 100000, 'total_price' => 500000, 'date' => '2026-11-06'],
        ];

        foreach ($approved as $row) {
            ProjectExpense::updateOrCreate(
                ['project_id' => $project->id, 'category' => $row['category'], 'name' => $row['name']],
                array_merge($row, [
                    'project_id' => $project->id,
                    'status' => ProjectExpense::STATUS_APPROVED,
                    'created_by' => $admin->id,
                    'approved_by' => $admin->id,
                    'approved_at' => now(),
                ])
            );
        }

        // Satu contoh pencatatan dari penggarap yang masih menunggu persetujuan.
        ProjectExpense::updateOrCreate(
            ['project_id' => $project->id, 'category' => 'pupuk', 'name' => 'Pupuk NPK tambahan'],
            [
                'project_id' => $project->id,
                'service_id' => null,
                'item_type' => 'NPK',
                'quantity' => 2,
                'unit' => 'sak',
                'unit_price' => 150000,
                'total_price' => 300000,
                'date' => '2026-11-18',
                'notes' => 'Usulan penggarap dari lapangan',
                'status' => ProjectExpense::STATUS_PENDING,
                'created_by' => $joko->id,
                'approved_by' => null,
                'approved_at' => null,
            ]
        );
    }
}
