<?php

namespace App\Support;

use App\Models\AgriculturalProject;
use App\Models\ProjectExpense;
use App\Models\Service;
use App\Models\User;

class ExpenseCreator
{
    /**
     * Buat pencatatan proyek dengan aturan Fase 2:
     * - Kategori sewa: tarif diambil dari master layanan, mengikuti status
     *   membership pemilik sawah (member = harga potongan, bukan = normal),
     *   lalu di-snapshot ke catatan agar laporan lama tidak berubah.
     * - Total = jumlah x harga satuan.
     * - Data dari admin (perusahaan) langsung approved; dari penggarap
     *   berstatus pending sampai di-approve perusahaan.
     */
    public static function create(array $data, AgriculturalProject $project, User $actor): ProjectExpense
    {
        $service = null;
        $unitPrice = (int) ($data['unit_price'] ?? 0);
        $unit = $data['unit'] ?? 'unit';

        if (isset(ProjectExpense::RENTAL_CATEGORIES[$data['category']])) {
            $service = Service::where('slug', ProjectExpense::RENTAL_CATEGORIES[$data['category']])
                ->where('is_active', true)
                ->first();

            if ($service) {
                // Harga sewa selalu mengikuti tarif resmi + status membership pemilik.
                $unitPrice = $service->priceFor($project->owner);
                $unit = $service->unit;
            }
        }

        $quantity = (float) $data['quantity'];
        $isAdmin = $actor->isAdmin();

        return ProjectExpense::create([
            'project_id' => $project->id,
            'service_id' => $service?->id,
            'category' => $data['category'],
            'name' => $data['name'],
            'item_type' => $data['item_type'] ?? null,
            'quantity' => $quantity,
            'unit' => $unit,
            'unit_price' => $unitPrice,
            'total_price' => (int) round($quantity * $unitPrice),
            'date' => $data['date'],
            'notes' => $data['notes'] ?? null,
            'status' => $isAdmin ? ProjectExpense::STATUS_APPROVED : ProjectExpense::STATUS_PENDING,
            'created_by' => $actor->id,
            'approved_by' => $isAdmin ? $actor->id : null,
            'approved_at' => $isAdmin ? now() : null,
        ]);
    }

    /** Aturan validasi form pencatatan (dipakai panel admin & webapp). */
    public static function rules(): array
    {
        return [
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(ProjectExpense::CATEGORIES))],
            'name' => ['required', 'string', 'max:255'],
            'item_type' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['nullable', 'string', 'max:30'],
            'unit_price' => ['nullable', 'integer', 'min:0'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
