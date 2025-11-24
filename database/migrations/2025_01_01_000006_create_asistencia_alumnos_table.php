<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(){
        Schema::create('asistencia_alumnos', function (Blueprint $table){
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->foreignId('aula_id')->constrained('aulas')->onDelete('cascade');
            $table->date('dia');
            $table->enum('estado', ['presente','ausente','tarde','justificado'])->default('presente');
            $table->string('lista_imagen')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('leccion_id')->nullable()->constrained('lecciones')->nullOnDelete();
            $table->timestamps();
            $table->unique(['alumno_id','dia']);
        });
    }
    public function down(){ Schema::dropIfExists('asistencia_alumnos'); }
};
