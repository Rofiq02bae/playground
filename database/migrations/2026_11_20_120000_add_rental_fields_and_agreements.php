<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jenis sewa proyek: bagi_hasil (dengan surat perjanjian) atau
     * tahunan (durasi 1-5 tahun dari tanggal mulai, tanpa surat).
     * Surat perjanjian yang sudah di-generate disimpan (nomor + PDF)
     * agar bisa diunduh ulang kapan pun dari detail proyek.
     */
    public function up(): void
    {
        Schema::table('agricultural_projects', function (Blueprint $table) {
            $table->string('rental_type', 20)->default('bagi_hasil')->after('status'); // bagi_hasil | tahunan
            $table->unsignedTinyInteger('duration_years')->nullable()->after('rental_type');
            $table->date('lease_end_date')->nullable()->after('duration_years');
            $table->unsignedBigInteger('annual_rental_price')->nullable()->after('lease_end_date'); // Rp per tahun (sementara, perlu validasi ulang)
        });

        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained('agricultural_projects')->cascadeOnDelete();
            $table->string('number'); // nomor surat, format dari config/perjanjian.php
            $table->string('witness1_name');
            $table->string('witness2_name');
            $table->date('agreement_date');
            $table->string('pdf_path'); // lokasi file PDF di storage lokal
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agreements');

        Schema::table('agricultural_projects', function (Blueprint $table) {
            $table->dropColumn(['rental_type', 'duration_years', 'lease_end_date', 'annual_rental_price']);
        });
    }
};
