<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Crea un utente senza ruolo
        User::create([
            'name' => 'Steven Manson (User)',
            'email' => 'user@aulab.it',
            'password' => Hash::make('password'),
            'is_writer' => false,
            'is_revisor' => false,
            'is_admin' => false,
        ]);

        // Crea un utente con ruolo writer
        User::create([
            'name' => "Daria Richardson (Writer)",
            'email' => 'writer@aulab.it',
            'password' => Hash::make('password'),
            'is_writer' => true,
            'is_revisor' => false,
            'is_admin' => false,
        ]);

        // Crea un utente con ruolo revisor
        User::create([
            'name' => "Antony Delgado (Revisor)",
            'email' => 'revisor@aulab.it',
            'password' => Hash::make('password'),
            'is_writer' => false,
            'is_revisor' => true,
            'is_admin' => false,
        ]);

        // Crea un amministratore
        User::create([
            'name' => 'Steve Lorren (Admin)',
            'email' => 'admin@aulab.it',
            'password' => Hash::make('password'),
            'is_writer' => false,
            'is_revisor' => false,
            'is_admin' => true,
        ]);

        // Crea un super amministratore con tutti i ruoli
        User::create([
            'name' => "Mario Bianchi (Super admin)",
            'email' => 'super.admin@aulab.it',
            'password' => Hash::make('password'),
            'is_writer' => true,
            'is_revisor' => true,
            'is_admin' => true,
        ]);
        // Crea un super amministratore con tutti i ruoli
        User::create([
            'name' => "Kevin Ross (Attacker)",
            'email' => 'kvrs@gmail.com',
            'password' => Hash::make('password'),
            'is_writer' => false,
            'is_revisor' => false,
            'is_admin' => false,
        ]);
    }
}
