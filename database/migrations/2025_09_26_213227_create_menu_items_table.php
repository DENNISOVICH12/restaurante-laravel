<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('categoria');               // ej: 'plato' | 'bebida' | 'postre'
            $table->decimal('precio', 10, 2);
            $table->string('imagen')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();                      // quita si no usas timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
