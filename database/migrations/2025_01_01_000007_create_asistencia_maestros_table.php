<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(){
        Schema::create('asistencia_maestros', function (Blueprint $table){
            $table->id();
            $table->foreignId('maestro_id')->constrained('users')->onDelete('cascade');
            $table->datetime('dia'); //fecha de desarrollo de la leccion
            //dias de la semana que asistio
            $table->string('dias_semana');
            $table->string('observaciones')->nullable();
            $table->enum('estado', ['presente','ausente','tarde','justificado'])->default('presente');
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('asistencia_maestros'); }
};
