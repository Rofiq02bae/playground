<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->orderBy('role')->orderBy('name');

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        return view('admin.users.index', [
            'users' => $query->paginate(20)->withQueryString(),
            'roles' => User::ROLES,
        ]);
    }

    public function create()
    {
        return view('admin.users.form', [
            'user' => new User(['role' => User::ROLE_PEMILIK]),
            'roles' => User::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateUser($request);
        $data['password'] = Hash::make($data['password']);
        $data['is_member'] = $request->boolean('is_member');
        $data['address'] = $this->composeAddress($data);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', [
            'user' => $user,
            'roles' => User::ROLES,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validateUser($request, $user);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_member'] = $request->boolean('is_member');
        $data['address'] = $this->composeAddress($data);
        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('success', 'Akun Anda sendiri tidak bisa dihapus.');
        }

        if ($user->isAdmin() && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->with('success', 'Admin terakhir tidak bisa dihapus.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(User::ROLES)],
            'username' => ['required_without:email', 'nullable', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user?->id)],
            'email' => ['required_without:username', 'nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'nik' => ['nullable', 'string', 'max:20'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'province_code' => ['nullable', 'string', 'max:13', Rule::exists('regions', 'kode')],
            'regency_code' => ['nullable', 'string', 'max:13', Rule::exists('regions', 'kode')],
            'district_code' => ['nullable', 'string', 'max:13', Rule::exists('regions', 'kode')],
            'village_code' => ['nullable', 'string', 'max:13', Rule::exists('regions', 'kode')],
            'hamlet' => ['nullable', 'string', 'max:255'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:6', 'confirmed'],
            'is_member' => ['nullable', 'boolean'],
        ]);
    }

    /** Alamat lengkap disimpan terkomposisi dari dropdown wilayah + dusun/RT/RW. */
    private function composeAddress(array $data): ?string
    {
        $province = $data['province_code'] ?? null;
        $regency = $data['regency_code'] ?? null;
        $district = $data['district_code'] ?? null;
        $village = $data['village_code'] ?? null;

        $names = Region::whereIn('kode', array_filter([$province, $regency, $district, $village]))
            ->pluck('nama', 'kode');

        return User::composeAddress($data, [
            'province' => $province ? ($names[$province] ?? null) : null,
            'regency' => $regency ? ($names[$regency] ?? null) : null,
            'district' => $district ? ($names[$district] ?? null) : null,
            'village' => $village ? ($names[$village] ?? null) : null,
        ]);
    }
}
