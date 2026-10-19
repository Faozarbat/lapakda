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
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();

            $table->string('nama');
            $table->unsignedBigInteger('harga'); // Disimpan dalam Rupiah bulat (bukan desimal) — hindari isu floating point

            // Path/UUID file foto (bukan nama asli dari HP user) — lihat checklist keamanan upload
            $table->string('foto')->nullable();

            $table->text('deskripsi')->nullable();
            $table->string('kategori_produk')->nullable(); // Teks bebas, misal "Makanan Berat", "Minuman"

            $table->boolean('status_tersedia')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
