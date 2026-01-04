<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anexo;

class AnexoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Anexo::firstOrCreate([
            'nombre' => 'Las Palmeras II',
            'direccion' => 'La Esperanza',
            'fecha_creacion' => '2025-01-01',
            'activo' => 1
        ]);

        Anexo::firstOrCreate([
            'nombre' => 'El Mirador de Las Palmeras',
            'direccion' => 'La Esperanza',
            'fecha_creacion' => '2025-01-01',
            'activo' => 1
        ]);
        Anexo::firstOrCreate([
            'nombre' => 'Barrio 4b',
            'direccion' => 'Alto Trujillo',
            'fecha_creacion' => '2025-01-01',
            'activo' => 1
        ]);
        Anexo::firstOrCreate([
            'nombre' => 'Ciudad de Dios',
            'direccion' => 'Alto Trujillo',
            'fecha_creacion' => '2025-01-01',
            'activo' => 1
        ]);
        Anexo::firstOrCreate([
            'nombre' => 'Barrio 4',
            'direccion' => 'Alto Trujillo',
            'fecha_creacion' => '2025-01-01',
            'activo' => 1
        ]);
    }
}
