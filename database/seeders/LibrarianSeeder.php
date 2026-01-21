<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use function Symfony\Component\Clock\now;

class LibrarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_accounts')->insert([
            'first_name'    => 'Admin',
            'last_name'     => 'Lib',
            'email'         => 'admin.lib@library.com',
            'password'      => Hash::make('admin1234'),
            'role'          => 'librarian',
            'course_id'     => 1,
            'date_joined'   => now(),
        ]);
    }
}
