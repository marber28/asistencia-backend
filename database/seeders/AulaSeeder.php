<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Aula;

class AulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Aula::firstOrCreate([
            'nombre' => 'Primeros pasos',
            'descripcion' => 'Aula para los niños que están dando sus primeros pasos en el aprendizaje.',
            'edad_min' => 2,
            'edad_max' => 3
        ]);
        Aula::firstOrCreate([
            'nombre' => 'Párvulos',
            'descripcion' => 'Aula para los párvulos que están comenzando su educación inicial.',
            'edad_min' => 4,
            'edad_max' => 6
        ]);
        Aula::firstOrCreate([
            'nombre' => 'Principiantes',
            'descripcion' => 'Aula para los niños que están iniciando su educación básica.',
            'edad_min' => 7,
            'edad_max' => 10
        ]);
        Aula::firstOrCreate([
            'nombre' => 'Primarios',
            'descripcion' => 'Aula para los estudiantes de educación primaria.',
            'edad_min' => 11,
            'edad_max' => 14
        ]);
    }
}
