<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Fixed accounts with predictable credentials
        User::create([
            'name'              => 'Admin Sistem',
            'email'             => 'admin@eaduan.my',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        $officers = [
            ['name' => 'Pegawai Ahmad Razif',   'email' => 'pegawai1@eaduan.my'],
            ['name' => 'Pegawai Siti Norfazila', 'email' => 'pegawai2@eaduan.my'],
            ['name' => 'Pegawai Hazwan Amirul',  'email' => 'pegawai3@eaduan.my'],
        ];

        foreach ($officers as $officer) {
            User::create([
                'name'              => $officer['name'],
                'email'             => $officer['email'],
                'password'          => Hash::make('password'),
                'role'              => 'officer',
                'email_verified_at' => now(),
            ]);
        }

        User::create([
            'name'              => 'Awam Test',
            'email'             => 'awam@eaduan.my',
            'password'          => Hash::make('password'),
            'role'              => 'complainant',
            'email_verified_at' => now(),
        ]);

        // Random complainants
        User::factory()->complainant()->count(15)->create();
    }
}
