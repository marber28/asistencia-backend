<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(){
        Schema::create('alumnos', function (Blueprint $table){
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('alumnos'); }
};
