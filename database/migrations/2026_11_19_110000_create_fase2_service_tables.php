<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fase 2 — Service: master layanan & harga, proyek/profil sawah,
     * dan pencatatan proyek dengan alur approval perusahaan.
     */
    public function up(): void
    {
        // Master layanan sewa + harga normal/member.
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('branch')->default('pertanian'); // pertanian | peternakan
            $table->string('unit')->default('ha');          // satuan tarif: ha, hari, unit, dll.
            $table->unsignedBigInteger('price_normal')->default(0);
            $table->unsignedBigInteger('price_member')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Proyek pertanian = profil sawah (satu proyek, satu lahan utama).
        Schema::create('agricultural_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users');                 // pemilik sawah
            $table->foreignId('cultivator_id')->nullable()->constrained('users'); // penggarap sawah
            $table->string('field_name');
            $table->string('location');
            $table->decimal('area_size', 10, 2); // luas dalam hektar
            $table->string('coordinates')->nullable();
            $table->string('crop_type');
            $table->string('crop_variety')->nullable();
            $table->date('start_date');
            $table->date('estimated_harvest_date')->nullable();
            $table->string('status')->default('persiapan'); // persiapan|berjalan|menunggu_panen|selesai|dibatalkan
            $table->unsignedTinyInteger('profit_share_company_percent')->default(50);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Pencatatan proyek: sewa alat, pupuk, obat, dan pengeluaran lain.
        // Data dari penggarap berstatus pending sampai di-approve perusahaan;
        // hanya yang approved yang masuk total/laporan resmi.
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('agricultural_projects')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services'); // terisi untuk kategori sewa
            $table->string('category'); // sewa_drone|sewa_mesin_tanam|pupuk|obat|tenaga_kerja|benih|transportasi|lainnya
            $table->string('name');
            $table->string('item_type')->nullable(); // jenis pupuk/obat, jenis layanan, dll.
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit')->default('unit');
            $table->unsignedBigInteger('unit_price')->default(0);
            $table->unsignedBigInteger('total_price')->default(0);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending|approved|rejected
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
        Schema::dropIfExists('agricultural_projects');
        Schema::dropIfExists('services');
    }
};
