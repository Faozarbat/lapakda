<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel untuk menyimpan pesan chat
        Schema::create('order_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // 2. Tambah kolom timer di tabel orders
        Schema::table('orders', function (Blueprint $table) {
            // Kapan chat 5 jam akan berakhir (null = belum berakhir / belum selesai)
            $table->timestamp('chat_expires_at')->nullable()->after('status');
            // Siapa yang memicu timer (buyer/seller)
            $table->string('chat_initiated_by')->nullable()->after('chat_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['chat_expires_at', 'chat_initiated_by']);
        });
        Schema::dropIfExists('order_messages');
    }
};