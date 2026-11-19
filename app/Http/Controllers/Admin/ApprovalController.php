<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectExpense;

class ApprovalController extends Controller
{
    /** Antrean pencatatan dari penggarap yang menunggu persetujuan perusahaan. */
    public function index()
    {
        $pending = ProjectExpense::query()
            ->where('status', ProjectExpense::STATUS_PENDING)
            ->with(['project.owner', 'creator'])
            ->latest('date')
            ->latest()
            ->paginate(20);

        return view('admin.approvals.index', [
            'pending' => $pending,
        ]);
    }
}
