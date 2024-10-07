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
        User::firstOrCreate(
            ['email' => 'johndoe@example.com'], // Check for existing user with this email
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'), // Only create if no user is found with the email
            ]
        );
    }

}
