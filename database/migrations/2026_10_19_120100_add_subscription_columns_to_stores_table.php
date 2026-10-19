<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dateTime('active_until')->nullable()->after('status_aktif');
            $table->enum('subscription_status', ['trial', 'discounted', 'active', 'archived'])
                  ->default('trial')->after('active_until');
            $table->boolean('open_fee_paid')->default(false)->after('subscription_status');
            
            // Relasi ke voucher (jika toko ini didaftarkan pakai voucher)
            $table->foreignId('voucher_id')->nullable()->after('open_fee_paid')
                  ->constrained('vouchers')->nullOnDelete();
                  
            $table->dateTime('subscription_started_at')->nullable()->after('voucher_id');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn([
                'active_until', 
                'subscription_status', 
                'open_fee_paid', 
                'voucher_id', 
                'subscription_started_at'
            ]);
        });
    }
};