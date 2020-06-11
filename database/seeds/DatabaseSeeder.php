<?php

use App\Todo;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $user = factory(User::class)->create([
            'first_name' => 'Javier',
            'last_name' => 'Estupinan',
            'email' => 'jestupinan@zeerbyte.com'
        ]);

        factory(Todo::class, 50)->create(['user_id' => $user]);
    }
}
