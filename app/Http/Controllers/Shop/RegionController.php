<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Data dropdown alamat bertingkat untuk checkout (user login).
     * Sama seperti versi admin, tetapi dapat diakses pembeli/pemilik/penggarap.
     * GET /produk/daerah?parent=34 -> kabupaten/kota, dst.
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
