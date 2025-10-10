<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('detalle_pedido') && !Schema::hasTable('pedido_detalles')) {
            Schema::rename('detalle_pedido', 'pedido_detalles');
        }

        if (Schema::hasTable('pedidos')) {
            $hasRestaurants = Schema::hasTable('restaurants');
            $hasClientes    = Schema::hasTable('clientes');

            if (!Schema::hasColumn('pedidos', 'restaurant_id')) {
                Schema::table('pedidos', function (Blueprint $table) use ($hasRestaurants) {
                    if ($hasRestaurants) {
                        $table->foreignId('restaurant_id')
                            ->nullable()
                            ->after('id')
                            ->constrained('restaurants')
                            ->nullOnDelete();
                    } else {
                        $table->unsignedBigInteger('restaurant_id')->nullable()->after('id');
                    }
                });
            }

            if (!Schema::hasColumn('pedidos', 'cliente_id')) {
                Schema::table('pedidos', function (Blueprint $table) use ($hasClientes) {
                    if ($hasClientes) {
                        $table->foreignId('cliente_id')
                            ->nullable()
                            ->after('restaurant_id')
                            ->constrained('clientes')
                            ->nullOnDelete();
                    } else {
                        $table->unsignedBigInteger('cliente_id')->nullable()->after('restaurant_id');
                    }
                });
            }

            if (!Schema::hasColumn('pedidos', 'estado')) {
                Schema::table('pedidos', function (Blueprint $table) {
                    $table->string('estado')->default('pendiente')->after('cliente_id');
                });
            }

            if (!Schema::hasColumn('pedidos', 'created_at')) {
                Schema::table('pedidos', function (Blueprint $table) {
                    $table->timestamp('created_at')->nullable()->after('estado');
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                });
            }

            if (Schema::hasColumn('pedidos', 'id_cliente')) {
                DB::table('pedidos')->whereNull('cliente_id')->update([
                    'cliente_id' => DB::raw('id_cliente'),
                ]);
            }

            if (Schema::hasColumn('pedidos', 'fecha') && Schema::hasColumn('pedidos', 'created_at')) {
                DB::table('pedidos')->whereNull('created_at')->update([
                    'created_at' => DB::raw('fecha'),
                ]);
            }

            if (Schema::hasColumn('pedidos', 'restaurant_id') && Schema::hasTable('restaurants')) {
                $defaultRestaurant = DB::table('restaurants')->orderBy('id')->value('id');
                if ($defaultRestaurant) {
                    DB::table('pedidos')->whereNull('restaurant_id')->update([
                        'restaurant_id' => $defaultRestaurant,
                    ]);
                }
            }
        }

        if (Schema::hasTable('pedido_detalles')) {
            $hasRestaurants = Schema::hasTable('restaurants');
            $hasMenuItems   = Schema::hasTable('menu_items');

            if (!Schema::hasColumn('pedido_detalles', 'restaurant_id')) {
                Schema::table('pedido_detalles', function (Blueprint $table) use ($hasRestaurants) {
                    if ($hasRestaurants) {
                        $table->foreignId('restaurant_id')
                            ->nullable()
                            ->after('id')
                            ->constrained('restaurants')
                            ->nullOnDelete();
                    } else {
                        $table->unsignedBigInteger('restaurant_id')->nullable()->after('id');
                    }
                });
            }

            if (!Schema::hasColumn('pedido_detalles', 'pedido_id')) {
                Schema::table('pedido_detalles', function (Blueprint $table) {
                    $table->unsignedBigInteger('pedido_id')->nullable()->after('restaurant_id');
                });
            }

            if (!Schema::hasColumn('pedido_detalles', 'menu_item_id')) {
                Schema::table('pedido_detalles', function (Blueprint $table) use ($hasMenuItems) {
                    if ($hasMenuItems) {
                        $table->foreignId('menu_item_id')
                            ->nullable()
                            ->after('pedido_id')
                            ->constrained('menu_items')
                            ->nullOnDelete();
                    } else {
                        $table->unsignedBigInteger('menu_item_id')->nullable()->after('pedido_id');
                    }
                });
            }

            if (!Schema::hasColumn('pedido_detalles', 'precio_unitario')) {
                Schema::table('pedido_detalles', function (Blueprint $table) {
                    $table->decimal('precio_unitario', 10, 2)->default(0)->after('cantidad');
                });
            }

            if (!Schema::hasColumn('pedido_detalles', 'importe')) {
                Schema::table('pedido_detalles', function (Blueprint $table) {
                    $table->decimal('importe', 10, 2)->default(0)->after('precio_unitario');
                });
            }

            if (!Schema::hasColumn('pedido_detalles', 'created_at')) {
                Schema::table('pedido_detalles', function (Blueprint $table) {
                    $table->timestamp('created_at')->nullable()->after('importe');
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                });
            }

            if (Schema::hasColumn('pedido_detalles', 'id_pedido')) {
                DB::table('pedido_detalles')->whereNull('pedido_id')->update([
                    'pedido_id' => DB::raw('id_pedido'),
                ]);
            }

            if (Schema::hasColumn('pedido_detalles', 'precio')) {
                DB::table('pedido_detalles')->whereNull('precio_unitario')->update([
                    'precio_unitario' => DB::raw('precio'),
                ]);
                DB::table('pedido_detalles')->whereNull('importe')->update([
                    'importe' => DB::raw('precio * cantidad'),
                ]);
            }

            if (Schema::hasColumn('pedido_detalles', 'restaurant_id') && Schema::hasColumn('pedido_detalles', 'pedido_id')) {
                $defaults = DB::table('pedidos')
                    ->select('id', 'restaurant_id')
                    ->whereNotNull('restaurant_id')
                    ->pluck('restaurant_id', 'id');

                if ($defaults->isNotEmpty()) {
                    foreach ($defaults as $pedidoId => $restaurantId) {
                        DB::table('pedido_detalles')
                            ->whereNull('restaurant_id')
                            ->where('pedido_id', $pedidoId)
                            ->update(['restaurant_id' => $restaurantId]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // Intentionally left blank: reverting could drop datos existentes.
    }
};
