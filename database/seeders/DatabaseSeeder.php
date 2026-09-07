<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('12345678'),
                'role'     => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'owner@gmail.com'],
            [
                'name'     => 'Owner',
                'password' => Hash::make('12345678'),
                'role'     => 'owner',
            ]
        );

        $this->call([
            ProductSeeder::class,
            MasterDataSeeder::class,
            TransactionSeeder::class,
            NoPerkiraanSeeder::class,
            AccountingSeeder::class,
        ]);
    }
}