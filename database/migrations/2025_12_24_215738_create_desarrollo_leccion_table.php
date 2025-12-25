<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('desarrollo_leccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leccion_id')->constrained('lecciones')->cascadeOnDelete();
            $table->string('versiculo_memorizado')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('ensenanza')->nullable();
            $table->text('motivacion')->nullable();
            $table->text('estrategias')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('pdf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desarrollo_leccion');
    }
};
