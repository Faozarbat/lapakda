<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();

            // Pemilik toko (akun user Laravel biasa). Dipakai untuk authorization
            // per-tenant lewat Policy — pastikan owner_id yang dicek, bukan asumsi frontend.
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('kecamatan_id')->constrained('kecamatans')->restrictOnDelete();

            $table->string('nama');
            $table->string('slug'); // Unik PER KECAMATAN, bukan unik global — lihat unique() composite di bawah

            $table->enum('kategori', ['resto', 'store']);

            $table->string('nomor_wa'); // Format internasional disarankan, misal 62812xxxxxxx
            $table->string('alamat');

            // Jam operasional disimpan sebagai JSON supaya fleksibel per hari.
            // Contoh isi: {"senin": ["08:00","22:00"], "selasa": ["08:00","22:00"], ...}
            $table->json('jam_operasional')->nullable();

            // Titik lokasi toko — WAJIB diisi saat registrasi (divalidasi di level aplikasi/form).
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Kalau true, konsumen wajib isi nomor meja saat checkout dine-in.
            $table->boolean('has_table_number')->default(false);

            $table->boolean('status_aktif')->default(true);

            $table->timestamps();

            // Slug toko unik per kecamatan (bukan unik global) — sesuai keputusan di PRODUCT_SPEC.md
            $table->unique(['kecamatan_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
