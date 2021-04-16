<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        User::create([
            'name'              => 'Mike Shu',
            'email'             => 'heliax44@gmail.com',
            'password'          => '$2y$10$LhNSFvbl4io60YcIaACTNOgY6tmxe9gEvIsnAlo8HSpzfdEoqnUdq',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Adriana',
            'email'             => 'acc574315@gmail.com',
            'password'          => '$2y$10$SWgwa9miU7MX92k4kunU7e9/dNzrL17xra6No.BqDcwIob/clOW9m',
            'email_verified_at' => now(),
        ]);
    }
}
