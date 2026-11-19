<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Batas poligon sawah hasil gambar di peta (Leaflet + Geoman),
     * disimpan sebagai GeoJSON Feature Polygon. Titik lokasi utama
     * tetap di kolom `coordinates` ("lat, lng" titik tengah poligon/pin).
     */
    public function up(): void
    {
        Schema::table('agricultural_projects', function (Blueprint $table) {
            $table->json('boundary_geojson')->nullable()->after('coordinates');
        });
    }

    public function down(): void
    {
        Schema::table('agricultural_projects', function (Blueprint $table) {
            $table->dropColumn('boundary_geojson');
        });
    }
};
