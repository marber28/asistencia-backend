<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(){
        Schema::create('alumnos', function (Blueprint $table){
            $table->id();
            $table->string('nombres');
            $table->string('apellidos')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['M','F']);
            $table->string('foto')->nullable();
            //campo foreign key de la tabla anexos
            $table->unsignedBigInteger('anexo_id');
            $table->foreign('anexo_id')->references('id')->on('anexos')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('alumnos'); }
};
