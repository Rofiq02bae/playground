<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catatan hasil panen per proyek (bisa banyak / bertahap).
     * total_revenue = quantity_kg x unit_price, dihitung saat simpan.
     * Dipakai laporan biaya produksi untuk laba bersih & bagi hasil.
     */
    public function up(): void
    {
        Schema::create('harvest_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('agricultural_projects')->cascadeOnDelete();
            $table->date('harvest_date');
            $table->decimal('quantity_kg', 12, 2);
            $table->unsignedBigInteger('unit_price'); // harga jual per kg (Rp)
            $table->unsignedBigInteger('total_revenue'); // quantity_kg x unit_price
            $table->string('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvest_records');
    }
};
