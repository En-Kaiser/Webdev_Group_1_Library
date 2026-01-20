<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            [
                'first_name' => 'Rei',
                'last_name' => 'Magpantay',
                'email' => 'rei@pup.edu.ph',
                'role' => 'librarian',
                'password' => Hash::make('admin123'),
                'date_joined' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Jarell',
                'last_name' => 'Fababier',
                'email' => 'jarell@pup.edu.ph',
                'role' => 'librarian',
                'password' => Hash::make('admin098'),
                'date_joined' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
