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
        $owners = User::where('role', User::ROLE_PEMILIK)
            ->with(['province', 'regency', 'district', 'village'])
            ->orderBy('name')->get();

        return view('admin.projects.form', [
            'project' => new AgriculturalProject(['status' => 'persiapan', 'profit_share_company_percent' => 50, 'rental_type' => 'bagi_hasil']),
            'owners' => $owners,
            'cultivators' => User::where('role', User::ROLE_PENGGARAP)->orderBy('name')->get(),
            'statuses' => AgriculturalProject::STATUSES,
            'ownersData' => $owners->map(fn (User $o) => [
                'id' => $o->id,
                'name' => $o->name,
                'nik' => $o->nik,
                'occupation' => $o->occupation,
                'address' => $o->address,
                'place' => collect([$o->village?->nama, $o->district ? 'Kec. '.$o->district->nama : null, $o->regency?->nama])->filter()->implode(', '),
            ])->values(),
            'nextNumber' => \App\Support\AgreementGenerator::nextNumber(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateProject($request);
        $witness1 = $data['witness1_name'] ?? null;
        $witness2 = $data['witness2_name'] ?? null;
        unset($data['witness1_name'], $data['witness2_name']);
        $data['created_by'] = Auth::id();

        $project = AgriculturalProject::create($data);

        $message = 'Proyek & profil sawah berhasil dibuat.';

        // Sewa bagi hasil + nama saksi lengkap dari modal -> surat langsung dibuat & disimpan.
        if ($project->isBagiHasil() && filled($witness1) && filled($witness2)) {
            try {
                $agreement = \App\Support\AgreementGenerator::generate($project, $witness1, $witness2, Auth::user());
                $message .= " Surat perjanjian nomor {$agreement->number} berhasil dibuat — unduh PDF-nya di halaman ini.";
            } catch (\Throwable $e) {
                report($e);
                $message .= ' Namun pembuatan surat perjanjian gagal; buat menyusul lewat bagian Surat Perjanjian di halaman ini.';
            }
        }

        return redirect()->route('admin.projects.show', $project)->with('success', $message);
    }

    public function show(AgriculturalProject $project)
    {
        $project->load(['owner', 'cultivator', 'agreement']);

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
            'harvests' => $project->harvests()->with('creator')->orderBy('harvest_date')->get(),
            'letterData' => $project->isBagiHasil() && ! $project->agreement
                ? \App\Support\AgreementGenerator::letterData($project)
                : null,
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
        $data = $this->validateProject($request);
        unset($data['witness1_name'], $data['witness2_name']);
        $project->update($data);

        return redirect()->route('admin.projects.show', $project)->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(AgriculturalProject $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil dihapus.');
    }

    private function validateProject(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'owner_id' => ['required', Rule::exists('users', 'id')->where('role', User::ROLE_PEMILIK)],
            'cultivator_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_PENGGARAP)],
            'field_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'area_size' => ['required', 'numeric', 'min:0.01'],
            'coordinates' => ['nullable', 'string', 'max:255'],
            'boundary_geojson' => ['nullable', 'string', function (string $attribute, mixed $value, \Closure $fail) {
                $decoded = json_decode($value, true);
                $geometry = ($decoded['type'] ?? null) === 'Feature' ? ($decoded['geometry'] ?? null) : $decoded;

                if (! is_array($geometry) || ($geometry['type'] ?? null) !== 'Polygon' || empty($geometry['coordinates'][0])) {
                    $fail('Batas sawah harus berupa GeoJSON poligon yang valid.');

                    return;
                }

                foreach ($geometry['coordinates'][0] as $point) {
                    [$lng, $lat] = $point + [0, 0];
                    if (! is_numeric($lng) || ! is_numeric($lat) || $lng < 94 || $lng > 142 || $lat < -12 || $lat > 7) {
                        $fail('Koordinat batas sawah berada di luar wilayah Indonesia.');

                        return;
                    }
                }
            }],
            'crop_type' => ['required', 'string', 'max:255'],
            'crop_variety' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'estimated_harvest_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(AgriculturalProject::STATUSES)],
            'rental_type' => ['required', Rule::in(AgriculturalProject::RENTAL_TYPES)],
            'duration_years' => ['nullable', 'required_if:rental_type,tahunan', 'integer', 'min:1', 'max:5'],
            'annual_rental_price' => ['nullable', 'integer', 'min:0'],
            'profit_share_company_percent' => ['nullable', 'required_if:rental_type,bagi_hasil', 'integer', 'min:0', 'max:100'],
            'witness1_name' => ['nullable', 'string', 'max:255'],
            'witness2_name' => ['nullable', 'string', 'max:255'],
        ]);

        $data['boundary_geojson'] = ! empty($data['boundary_geojson'])
            ? json_decode($data['boundary_geojson'], true)
            : null;

        if (($data['rental_type'] ?? 'bagi_hasil') === 'tahunan') {
            // Sewa tahunan: akhir sewa = tanggal mulai + durasi; bagi hasil tidak berlaku.
            $data['lease_end_date'] = \Carbon\Carbon::parse($data['start_date'])
                ->addYears((int) $data['duration_years'])->toDateString();
            $data['estimated_harvest_date'] = null;
            $data['profit_share_company_percent'] = 0;
        } else {
            $data['duration_years'] = null;
            $data['lease_end_date'] = null;
            $data['annual_rental_price'] = null;
        }

        return $data;
    }
}
