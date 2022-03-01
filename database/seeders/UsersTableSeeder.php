<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'id' => 1,
            'name' => 'admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('123'),
            'is_admin' => true,
        ]);

        User::create([
            'id' => 2,
            'name' => 'customer',
            'email' => 'customer@test.com',
            'password' => Hash::make('123'),
            'is_admin' => false,
        ]);
    }
}
