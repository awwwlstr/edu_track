<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Users;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Users::updateOrCreate(
            ['email' => 'admin@sekolah.com'],
            [
                'nama'     => 'Administrator',
                'nip'      => 'ADMIN001',
                'email'    => 'admin@sekolah.com',
                'password' => bcrypt('admin123'),
                'role'     => 'admin',
            ]
        );
    }
}