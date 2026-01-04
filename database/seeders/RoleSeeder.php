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
        Role::firstOrCreate(['name' => 'responsable']);

        // crear usuario admin (ajusta email/password)
        $u = User::firstOrCreate(['email' => 'admin@asistencia.com'], [
            'name' => 'Admin',
            'lastname' => 'Admin',
            'enabled' => true,
            'visible' => false,
            'password' => bcrypt('123456')
        ]);
        $u->assignRole('admin');

        $pl = User::firstOrCreate(['email' => 'palmeras@asistencia.com'], [
            'name' => 'Doris',
            'lastname' => 'Palmeras',
            'enabled' => true,
            'visible' => true,
            'password' => bcrypt('123456')
        ]);
        $pl->assignRole('responsable');

        $pal = User::firstOrCreate(['email' => 'altotrujillo@asistencia.com'], [
            'name' => 'Antonio',
            'lastname' => 'Cordova',
            'enabled' => true,
            'visible' => true,
            'password' => bcrypt('123456')
        ]);
        $pal->assignRole('responsable');
    }
}
