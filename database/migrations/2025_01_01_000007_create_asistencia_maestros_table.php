<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(){
        Schema::create('asistencia_maestros', function (Blueprint $table){
            $table->id();
            $table->foreignId('maestro_id')->constrained('maestros')->onDelete('cascade');
            $table->date('dia');
            $table->foreignId('leccion_id')->nullable()->constrained('lecciones')->nullOnDelete();
            $table->enum('estado', ['presente','ausente','tarde'])->default('presente');
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('asistencia_maestros'); }
};
