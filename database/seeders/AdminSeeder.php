<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['nomor_hp' => '6281999263536'], 
            [
                'name' => 'Super Admin',
                'email' => 'admin@lapakda.com',
                'password' => Hash::make('nikolas'), 
                'is_admin' => true,
            ]
        );
    }
}