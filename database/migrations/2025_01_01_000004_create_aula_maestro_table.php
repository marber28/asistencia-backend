<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(){
        Schema::create('aula_maestro', function (Blueprint $table){
            $table->id();
            $table->foreignId('aula_id')->constrained('aulas')->onDelete('cascade');
            $table->foreignId('maestro_id')->constrained('maestros')->onDelete('cascade');
            $table->date('desde')->nullable();
            $table->date('hasta')->nullable();
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('aula_maestro'); }
};
