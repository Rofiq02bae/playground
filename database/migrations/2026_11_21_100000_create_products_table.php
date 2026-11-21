<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category'); // pertanian | peternakan
            $table->string('type')->default('barang'); // barang | jasa
            $table->unsignedBigInteger('price')->default(0);
            $table->integer('stock')->nullable(); // null = tidak dibatasi stok (jasa)
            $table->string('unit')->default('pcs'); // kg, ha, unit, pcs, ...
            $table->string('photo_path')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('aktif'); // draft | aktif | arsip
            $table->foreignId('harvest_project_id')->nullable()->constrained('agricultural_projects')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
