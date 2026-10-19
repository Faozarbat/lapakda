<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            // Terikat ke session guest, BUKAN user_id — konsumen tidak wajib login.
            $table->string('session_id')->index();

            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();

            $table->timestamps();

            // Satu session cuma boleh punya 1 cart aktif per toko
            // (beda toko = beda cart, sesuai keputusan di PRODUCT_SPEC.md bagian 4)
            $table->unique(['session_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
