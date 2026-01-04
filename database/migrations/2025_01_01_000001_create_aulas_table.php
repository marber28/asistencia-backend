<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(){
        Schema::create('aulas', function (Blueprint $table){
            $table->id();
            $table->string('nombre');
            $table->integer('edad_min')->nullable();
            $table->integer('edad_max')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }
    public function down(){
        Schema::dropIfExists('aulas');
    }
};
