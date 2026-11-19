<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgriculturalProject;
use App\Models\ProjectExpense;
use App\Support\ExpenseCreator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /** Admin menambah pencatatan: langsung approved. */
    public function store(Request $request, AgriculturalProject $project)
    {
        $data = $request->validate(ExpenseCreator::rules());

        ExpenseCreator::create($data, $project, Auth::user());

        return back()->with('success', 'Pencatatan ditambahkan dan langsung disetujui (input perusahaan).');
    }

    public function approve(ProjectExpense $expense)
    {
        $expense->update([
            'status' => ProjectExpense::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_note' => null,
        ]);

        return back()->with('success', 'Pencatatan disetujui dan masuk laporan resmi.');
    }

    public function reject(Request $request, ProjectExpense $expense)
    {
        $data = $request->validate([
            'rejection_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $expense->update([
            'status' => ProjectExpense::STATUS_REJECTED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_note' => $data['rejection_note'] ?? null,
        ]);

        return back()->with('success', 'Pencatatan ditolak.');
    }

    public function destroy(ProjectExpense $expense)
    {
        $expense->delete();

        return back()->with('success', 'Pencatatan dihapus.');
    }
}
