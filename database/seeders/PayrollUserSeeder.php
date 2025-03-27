<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PayrollUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Payroll Manager',
            'email' => 'payroll@example.com',
            'password' => Hash::make('password123'),
            'role' => 'payroll',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
