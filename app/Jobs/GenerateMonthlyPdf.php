<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\PDF as Dompdf;
use App\Models\AsistenciaAlumno;
use App\Models\Aula;
use Illuminate\Support\Facades\Storage;

class GenerateMonthlyPdf implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $month;
    public $aula_id;
    public $user;

    public function __construct($month, $aula_id, $user)
    {
        $this->month = $month; // formato YYYY-MM
        $this->aula_id = $aula_id;
        $this->user = $user;
    }


    public function handle(Dompdf $pdf)
    {
        [$year, $mon] = explode('-', $this->month);
        $start = "$year-$mon-01";
        $end = date('Y-m-t', strtotime($start));


        $aula = Aula::find($this->aula_id);
        $asistencias = AsistenciaAlumno::with('alumno')
            ->where('aula_id', $this->aula_id)
            ->whereBetween('dia', [$start, $end])
            ->orderBy('dia')
            ->get()
            ->groupBy('dia');

        $view = view('pdfs.monthly', [
            'aula' => $aula,
            'asistencias' => $asistencias,
            'month' => $this->month,
        ])->render();

        $pdf = \Barryvdh\DomPDF\Facade\pdf()->loadHTML($view)->setPaper('a4', 'portrait');


        $filename = "attendance_{$this->aula_id}_{$this->month}.pdf";
        Storage::disk('public')->put('reports/' . $filename, $pdf->output());
    }
}
