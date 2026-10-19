<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor_hp')->unique()->after('name');

            // Disiapkan untuk upgrade OTP nanti — paralel dengan email_verified_at
            // bawaan Laravel. Belum dipakai/di-enforce sekarang (belum ada OTP),
            // tapi kolomnya sudah ada supaya tidak perlu migrasi ulang nanti.
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
        });

        // Email jadi OPSIONAL. Di-drop lalu di-re-add (bukan ->change()) supaya
        // tidak butuh dependency tambahan (doctrine/dbal) hanya untuk 1 migrasi ini.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('nomor_hp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nomor_hp', 'phone_verified_at']);
        });
    }
};
