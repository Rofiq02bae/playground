<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgriculturalProject;
use App\Models\HarvestRecord;
use Illuminate\Http\Request;

class HarvestController extends Controller
{
    /** Catat hasil panen (bisa bertahap / multi-panen per proyek). */
    public function store(Request $request, AgriculturalProject $project)
    {
        $data = $request->validate([
            'harvest_date' => ['required', 'date'],
            'quantity_kg' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $project->harvests()->create([
            ...$data,
            'total_revenue' => (int) round($data['quantity_kg'] * $data['unit_price']),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Hasil panen berhasil dicatat.');
    }

    public function destroy(HarvestRecord $harvest)
    {
        $harvest->delete();

        return back()->with('success', 'Catatan panen dihapus.');
    }
}
