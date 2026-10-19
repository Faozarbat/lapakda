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
            $table->foreignId('store_id')->constrained('stores')->restrictOnDelete();

            $table->string('nomor_order')->unique(); // Format pendek, misal LPK-0001, digenerate di service layer

            $table->enum('fulfillment_type', ['self_pickup', 'delivery']);

            // Hanya relevan kalau fulfillment_type = delivery
            $table->decimal('delivery_latitude', 10, 7)->nullable();
            $table->decimal('delivery_longitude', 10, 7)->nullable();
            $table->text('delivery_note')->nullable(); // Catatan tambahan alamat, misal "pagar hijau"

            $table->enum('status', [
                'menunggu_konfirmasi',
                'dikonfirmasi',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_konfirmasi');

            // Disiapkan untuk Midtrans (belum aktif digunakan sekarang — lihat PRODUCT_SPEC.md bagian 9)
            $table->enum('payment_status', ['unpaid', 'pending', 'paid'])->default('unpaid');
            $table->enum('payment_method', ['cod', 'midtrans'])->default('cod');

            // Disiapkan untuk rencana fee Rp500/checkout (belum aktif — lihat PRODUCT_SPEC.md bagian 8)
            $table->unsignedInteger('platform_fee')->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes(); // Order tidak pernah dihapus permanen — lihat checklist keamanan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
