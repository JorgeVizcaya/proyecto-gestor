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
        Schema::create('almacentarea', function (Blueprint $table) {
            $table->id();
            $table->string('titulo'); 
            $table->text('descripcion'); 
            $table->enum('estado',['pendiente','en_progreso','completada','cancelada'])->default('pendiente'); 
            $table->enum('prioridad',['baja','media','alta','urgente'])->default('baja');
            $table->date('fecha_limite');
            $table->date('completada_el')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('almacentarea');
    }
};
