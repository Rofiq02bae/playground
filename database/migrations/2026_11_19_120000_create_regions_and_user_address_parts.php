<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data wilayah Indonesia (sumber: cahyadsn/wilayah, Kepmendagri
     * No 300.2.2-2138 Tahun 2025) untuk dropdown alamat bertingkat:
     * provinsi -> kabupaten/kota -> kecamatan -> kelurahan/desa.
     * Kode bersifat hierarkis: 34 | 34.04 | 34.04.01 | 34.04.01.2001.
     */
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->string('kode', 13)->primary();
            $table->string('nama', 100);
            $table->unsignedTinyInteger('level'); // 1 provinsi, 2 kabupaten/kota, 3 kecamatan, 4 kelurahan/desa
            $table->string('parent_kode', 13)->nullable()->index();
            $table->index('nama');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('province_code', 13)->nullable();
            $table->string('regency_code', 13)->nullable();
            $table->string('district_code', 13)->nullable();
            $table->string('village_code', 13)->nullable();
            $table->string('hamlet')->nullable(); // dusun (teks bebas)
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['province_code', 'regency_code', 'district_code', 'village_code', 'hamlet', 'rt', 'rw']);
        });

        Schema::dropIfExists('regions');
    }
};
