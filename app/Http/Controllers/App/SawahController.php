<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AgriculturalProject;
use App\Models\ProjectExpense;
use App\Models\Service;
use App\Support\ExpenseCreator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SawahController extends Controller
{
    public function index()
    {
        $projects = AgriculturalProject::query()
            ->visibleTo(Auth::user())
            ->with(['owner', 'cultivator'])
            ->withSum(['expenses as approved_total' => fn ($q) => $q->where('status', ProjectExpense::STATUS_APPROVED)], 'total_price')
            ->withCount(['expenses as pending_count' => fn ($q) => $q->where('status', ProjectExpense::STATUS_PENDING)])
            ->latest()
            ->get();

        return view('app.sawah.index', ['projects' => $projects]);
    }

    public function profile(){
        $profiles = AgriculturalProject::query()
            ->visibleTo(Auth::user())
            ->with(['owner', 'cultivator'])
            ->withSum(['expenses as approved_total' => fn ($q) => $q->where('status', ProjectExpense::STATUS_APPROVED)], 'total_price')
            ->withCount(['expenses as pending_count' => fn ($q) => $q->where('status', ProjectExpense::STATUS_PENDING)])
            ->latest()
            ->get();

        return view('app.layouts.app', ['profiles' => $profiles]);
    }

    public function show(AgriculturalProject $project)
    {
        if (! $project->isVisibleTo(Auth::user())) {
            abort(404);
        }

        $project->load(['owner', 'cultivator']);

        return view('app.sawah.show', [
            'project' => $project,
            'expenses' => $project->expenses()->with(['creator'])->latest('date')->latest()->paginate(20),
            'approvedTotal' => $project->approvedTotal(),
            'categories' => ProjectExpense::CATEGORIES,
            'services' => Service::where('is_active', true)->get(),
            'canAdd' => $project->canAddExpense(Auth::user()),
        ]);
    }

    /** Penggarap (atau admin) menambah pencatatan dari webapp: status pending untuk penggarap. */
    public function storeExpense(Request $request, AgriculturalProject $project)
    {
        if (! $project->isVisibleTo(Auth::user())) {
            abort(404);
        }

        if (! $project->canAddExpense(Auth::user())) {
            abort(403, 'Role pemilik hanya dapat melihat data.');
        }

        $data = $request->validate(ExpenseCreator::rules());

        $expense = ExpenseCreator::create($data, $project, Auth::user());

        $message = $expense->status === ProjectExpense::STATUS_APPROVED
            ? 'Pencatatan ditambahkan dan langsung disetujui.'
            : 'Pencatatan terkirim. Menunggu persetujuan perusahaan sebelum masuk laporan resmi.';

        return back()->with('success', $message);
    }
}
