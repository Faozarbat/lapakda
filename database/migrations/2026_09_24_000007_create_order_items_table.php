<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            // Nullable + nullOnDelete: produk boleh dihapus di kemudian hari,
            // histori order lama tetap utuh berkat snapshot nama & harga di bawah.
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            $table->string('nama_produk_saat_beli'); // Snapshot nama produk, bukan referensi live
            $table->unsignedBigInteger('harga_saat_beli'); // Snapshot harga saat checkout — WAJIB, lihat PRODUCT_SPEC.md
            $table->unsignedInteger('qty');

            // Hanya relevan kalau order.fulfillment_type = self_pickup
            $table->enum('consumption_type', ['dine_in', 'takeaway'])->nullable();

            // Wajib diisi kalau store.has_table_number = true DAN consumption_type = dine_in
            $table->string('table_number')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
