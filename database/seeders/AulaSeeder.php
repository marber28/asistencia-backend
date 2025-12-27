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
            'edad_max' => 4
        ]);
        Aula::firstOrCreate([
            'nombre' => 'Párvulos',
            'descripcion' => 'Aula para los párvulos que están comenzando su educación inicial.',
            'edad_min' => 5,
            'edad_max' => 7
        ]);
        Aula::firstOrCreate([
            'nombre' => 'Principiantes',
            'descripcion' => 'Aula para los niños que están iniciando su educación básica.',
            'edad_min' => 7,
            'edad_max' => 9
        ]);
        Aula::firstOrCreate([
            'nombre' => 'Primarios',
            'descripcion' => 'Aula para los estudiantes de educación primaria.',
            'edad_min' => 10,
            'edad_max' => 12
        ]);

        Aula::firstOrCreate([
            'nombre' => 'Adolescentes',
            'descripcion' => 'Aula para los estudiantes de educación secundaria.',
            'edad_min' => 13,
            'edad_max' => 15
        ]);
    }
}
