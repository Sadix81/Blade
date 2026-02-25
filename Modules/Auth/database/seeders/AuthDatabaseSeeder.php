<?php

namespace Modules\Auth\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AuthDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'lastname' => 'admin',
            'email' => 'admin@gmail.com',
            'mobile' => '12345678901',
            'password' => password_hash('@Dmin123', PASSWORD_DEFAULT),
            'country' => 'Iran',
            'province' => 'Golestan',
            'city' => 'Gorgan',
            'address' => 'AAAAAAAAAAA',
            'codepost' => '1234#232323',
            'email_verified_at' => Carbon::now(),
        ]);

        User::create([
            'username' => 'sadra',
            'lastname' => 'sadra',
            'email' => 'zsadra3@gmail.com',
            'mobile' => '09031111111',
            'password' => password_hash('@Dmin123', PASSWORD_DEFAULT),
            'twofactor' => true,
            'country' => 'Iran',
            'province' => 'Golestan',
            'city' => 'Gorgan',
            'address' => 'AAAAAAAAAAA',
            'codepost' => '1234#232323',
            'email_verified_at' => Carbon::now(),
        ]);

    }
}
