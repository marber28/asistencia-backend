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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('vista');
            $table->text('detalle')->nullable();
            $table->string('type', 20)->default('info');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Datos adicionales (opcional)
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index('vista');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
