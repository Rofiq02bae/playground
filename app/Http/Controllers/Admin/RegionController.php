<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Data untuk dropdown alamat bertingkat di form pengguna.
     * GET /admin/regions/children            -> daftar provinsi
     * GET /admin/regions/children?parent=34  -> kabupaten/kota di provinsi itu
     * GET /admin/regions/children?parent=34.04 -> kecamatan, dst.
     */
    public function children(Request $request): JsonResponse
    {
        $parent = $request->query('parent');

        if ($parent !== null && ! preg_match('/^\d{2}(\.\d{2}){0,2}(\.\d{4})?$/', (string) $parent)) {
            return response()->json([], 422);
        }

        $regions = Region::childrenOf($parent ?: null)
            ->orderBy('nama')
            ->get(['kode', 'nama']);

        return response()->json($regions);
    }
}
