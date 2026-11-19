<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgriculturalProject;
use App\Models\ProjectExpense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = AgriculturalProject::query()
            ->with(['owner', 'cultivator'])
            ->withSum(['expenses as approved_total' => fn ($q) => $q->where('status', ProjectExpense::STATUS_APPROVED)], 'total_price')
            ->withCount(['expenses as pending_count' => fn ($q) => $q->where('status', ProjectExpense::STATUS_PENDING)])
            ->latest();

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('field_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('owner', fn ($o) => $o->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('admin.projects.index', [
            'projects' => $query->paginate(15)->withQueryString(),
            'statuses' => AgriculturalProject::STATUSES,
        ]);
    }

    public function create()
    {
        return view('admin.projects.form', [
            'project' => new AgriculturalProject(['status' => 'persiapan', 'profit_share_company_percent' => 50]),
            'owners' => User::where('role', User::ROLE_PEMILIK)->orderBy('name')->get(),
            'cultivators' => User::where('role', User::ROLE_PENGGARAP)->orderBy('name')->get(),
            'statuses' => AgriculturalProject::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateProject($request);
        $data['created_by'] = Auth::id();

        $project = AgriculturalProject::create($data);

        return redirect()->route('admin.projects.show', $project)->with('success', 'Proyek & profil sawah berhasil dibuat.');
    }

    public function show(AgriculturalProject $project)
    {
        $project->load(['owner', 'cultivator']);

        $expenses = $project->expenses()->with(['creator', 'service'])->latest('date')->latest()->paginate(20);

        $byCategory = $project->approvedExpenses()
            ->selectRaw('category, SUM(total_price) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('admin.projects.show', [
            'project' => $project,
            'expenses' => $expenses,
            'byCategory' => $byCategory,
            'approvedTotal' => $project->approvedTotal(),
            'categories' => ProjectExpense::CATEGORIES,
            'services' => \App\Models\Service::where('is_active', true)->get(),
        ]);
    }

    public function edit(AgriculturalProject $project)
    {
        return view('admin.projects.form', [
            'project' => $project,
            'owners' => User::where('role', User::ROLE_PEMILIK)->orderBy('name')->get(),
            'cultivators' => User::where('role', User::ROLE_PENGGARAP)->orderBy('name')->get(),
            'statuses' => AgriculturalProject::STATUSES,
        ]);
    }

    public function update(Request $request, AgriculturalProject $project)
    {
        $project->update($this->validateProject($request));

        return redirect()->route('admin.projects.show', $project)->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(AgriculturalProject $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil dihapus.');
    }

    private function validateProject(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'owner_id' => ['required', Rule::exists('users', 'id')->where('role', User::ROLE_PEMILIK)],
            'cultivator_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_PENGGARAP)],
            'field_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'area_size' => ['required', 'numeric', 'min:0.01'],
            'coordinates' => ['nullable', 'string', 'max:255'],
            'crop_type' => ['required', 'string', 'max:255'],
            'crop_variety' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'estimated_harvest_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(AgriculturalProject::STATUSES)],
            'profit_share_company_percent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);
    }
}
