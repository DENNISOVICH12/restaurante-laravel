<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pedido_detalles', function (Blueprint $table) {
            $table->id();

            // Multitenencia: cada detalle pertenece a un restaurante
            $table->foreignId('restaurant_id')
                  ->constrained('restaurants')
                  ->cascadeOnDelete();

            // Relaciones: detalle pertenece a un pedido y a un ítem (plato/bebida/menú)
            $table->foreignId('pedido_id')
                  ->constrained('pedidos')
                  ->cascadeOnDelete();

            // Si usas una tabla de ítems de menú:
            $table->foreignId('menu_item_id')->nullable()
                  ->constrained('menu_items')->nullOnDelete();

            // Campos del detalle
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('importe', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_detalles');
    }
};
