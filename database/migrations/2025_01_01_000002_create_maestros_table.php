<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(){
        Schema::create('maestros', function (Blueprint $table){
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable()->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        //tabla muchos a muchos entre maestros y anexos
        Schema::create('anexo_maestro_aula', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('anexo_id');
            $table->unsignedBigInteger('maestro_id');
            $table->unsignedBigInteger('aula_id');
            $table->boolean('current')->default(false);
            $table->foreign('anexo_id')->references('id')->on('anexos')->onDelete('cascade');
            $table->foreign('maestro_id')->references('id')->on('maestros')->onDelete('cascade');
            $table->foreign('aula_id')->references('id')->on('aulas')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down(){
        Schema::dropIfExists('anexo_maestro_aula');
        Schema::dropIfExists('maestros');
    }
};
