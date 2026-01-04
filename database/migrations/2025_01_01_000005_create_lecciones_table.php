<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(){
        Schema::create('lecciones', function (Blueprint $table){
            $table->id();
            $table->string('titulo');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('versiculo')->nullable();
            $table->string('archivo_pdf')->nullable();
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('lecciones'); }
};
