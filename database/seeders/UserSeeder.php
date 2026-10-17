<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = config('auth.local_admin_password');
        if (! is_string($password) || strlen($password) < 8) {
            throw new \RuntimeException('Isi LOCAL_ADMIN_PASSWORD di .env dengan minimal 8 karakter sebelum menjalankan UserSeeder.');
        }

        User::create([
            'name' => 'Admin KoperasiKu',
            'email' => 'admin@koperasiku.id',
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);
    }
}
