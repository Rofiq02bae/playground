<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // kode pesanan = order_id Midtrans
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('menunggu_pembayaran'); // menunggu_pembayaran|dibayar|diproses|dikirim|selesai|dibatalkan
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('shipping_cost')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            // Penerima & alamat tujuan (snapshot saat checkout)
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('province_code')->nullable();
            $table->string('regency_code')->nullable();
            $table->string('district_code')->nullable();
            $table->string('village_code')->nullable();
            $table->string('hamlet')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('address_text');
            $table->string('postal_code', 10);
            // Pengiriman (hanya bila ada barang)
            $table->string('courier_company')->nullable();
            $table->string('courier_service')->nullable();
            $table->string('waybill')->nullable(); // nomor resi
            $table->string('biteship_order_id')->nullable();
            // Pembayaran Midtrans
            $table->string('payment_status')->nullable(); // transaction_status terakhir dari Midtrans
            $table->string('payment_type')->nullable();
            $table->string('snap_token')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // batas bayar (24 jam)
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name'); // snapshot
            $table->string('product_type'); // barang | jasa (snapshot)
            $table->string('unit'); // snapshot
            $table->unsignedBigInteger('price'); // snapshot harga satuan
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
