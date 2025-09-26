<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('usuario')->unique();
            $table->string('password');
            $table->string('nombre');
            $table->string('apellido')->nullable();
            $table->string('correo')->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps(); // quita si no quieres timestamps (y en el modelo usa public $timestamps = false)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
