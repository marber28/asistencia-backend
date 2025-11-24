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
            $table->string('email')->nullable()->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('maestros'); }
};
