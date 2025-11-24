<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'maestro']);
        Role::firstOrCreate(['name' => 'recepcionista']);

        // crear usuario admin (ajusta email/password)
        $u = User::firstOrCreate(['email' => 'admin@asistencia.com'], [
            'name' => 'Admin',
            'password' => bcrypt('123456')
        ]);
        $u->assignRole('admin');
    }
}
