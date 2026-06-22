<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'pushleveling@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('eKtHBC!+93agrsHd5gyf@G&St(pnmR'),
                'is_admin' => true,
                'level' => 1,
                'xp_total' => 0,
            ]
        );
    }
}
