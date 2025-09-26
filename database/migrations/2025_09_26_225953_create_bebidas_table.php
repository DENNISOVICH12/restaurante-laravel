<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bebidas', function (Blueprint $table) {
            $table->id();

            // multitenencia
            $table->foreignId('restaurant_id')
                  ->constrained('restaurants')
                  ->cascadeOnDelete();

            // datos de la bebida
            $table->string('nombre');
            $table->decimal('precio', 10, 2)->default(0);
            $table->boolean('disponible')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bebidas');
    }
};
