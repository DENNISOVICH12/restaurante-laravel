<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $this->ensurePdoDriver();


        if ($this->hasTable('detalle_pedido') && !$this->hasTable('pedido_detalles')) {
            Schema::rename('detalle_pedido', 'pedido_detalles');
        }

        if ($this->hasTable('pedidos')) {
            $hasRestaurants = $this->hasTable('restaurants');
            $hasClientes    = $this->hasTable('clientes');

            if (!$this->hasColumn('pedidos', 'restaurant_id')) {

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

            if (!$this->hasColumn('pedidos', 'cliente_id')) {

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

            if (!$this->hasColumn('pedidos', 'estado')) {

                Schema::table('pedidos', function (Blueprint $table) {
                    $table->string('estado')->default('pendiente')->after('cliente_id');
                });
            }

            if (!$this->hasColumn('pedidos', 'created_at')) {

                Schema::table('pedidos', function (Blueprint $table) {
                    $table->timestamp('created_at')->nullable()->after('estado');
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                });
            }

            if ($this->hasColumn('pedidos', 'id_cliente')) {

                DB::table('pedidos')->whereNull('cliente_id')->update([
                    'cliente_id' => DB::raw('id_cliente'),
                ]);
            }

            if ($this->hasColumn('pedidos', 'fecha') && $this->hasColumn('pedidos', 'created_at')) {

                DB::table('pedidos')->whereNull('created_at')->update([
                    'created_at' => DB::raw('fecha'),
                ]);
            }

            if ($this->hasColumn('pedidos', 'restaurant_id') && $this->hasTable('restaurants')) {

                $defaultRestaurant = DB::table('restaurants')->orderBy('id')->value('id');
                if ($defaultRestaurant) {
                    DB::table('pedidos')->whereNull('restaurant_id')->update([
                        'restaurant_id' => $defaultRestaurant,
                    ]);
                }
            }
        }


        if ($this->hasTable('pedido_detalles')) {
            $hasRestaurants = $this->hasTable('restaurants');
            $hasMenuItems   = $this->hasTable('menu_items');

            if (!$this->hasColumn('pedido_detalles', 'restaurant_id')) {

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

            if (!$this->hasColumn('pedido_detalles', 'pedido_id')) {

                Schema::table('pedido_detalles', function (Blueprint $table) {
                    $table->unsignedBigInteger('pedido_id')->nullable()->after('restaurant_id');
                });
            }

            if (!$this->hasColumn('pedido_detalles', 'menu_item_id')) {

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

            if (!$this->hasColumn('pedido_detalles', 'precio_unitario')) {

                Schema::table('pedido_detalles', function (Blueprint $table) {
                    $table->decimal('precio_unitario', 10, 2)->default(0)->after('cantidad');
                });
            }

            if (!$this->hasColumn('pedido_detalles', 'importe')) {

                Schema::table('pedido_detalles', function (Blueprint $table) {
                    $table->decimal('importe', 10, 2)->default(0)->after('precio_unitario');
                });
            }

            if (!$this->hasColumn('pedido_detalles', 'created_at')) {

                Schema::table('pedido_detalles', function (Blueprint $table) {
                    $table->timestamp('created_at')->nullable()->after('importe');
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                });
            }

            if ($this->hasColumn('pedido_detalles', 'id_pedido')) {

                DB::table('pedido_detalles')->whereNull('pedido_id')->update([
                    'pedido_id' => DB::raw('id_pedido'),
                ]);
            }

            if ($this->hasColumn('pedido_detalles', 'precio')) {

                DB::table('pedido_detalles')->whereNull('precio_unitario')->update([
                    'precio_unitario' => DB::raw('precio'),
                ]);
                DB::table('pedido_detalles')->whereNull('importe')->update([
                    'importe' => DB::raw('precio * cantidad'),
                ]);
            }

            if ($this->hasColumn('pedido_detalles', 'restaurant_id') && $this->hasColumn('pedido_detalles', 'pedido_id')) {

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

    private function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (QueryException $e) {
            return $this->retryHasTable($table, $e);
        }
    }

    private function hasColumn(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (QueryException $e) {
            return $this->retryHasColumn($table, $column, $e);
        }
    }

    private function retryHasTable(string $table, QueryException $exception): bool
    {
        $connection = Schema::getConnection();
        $driver     = $connection->getDriverName();

        if ($driver === 'sqlite') {
            $result = $connection->select(
                "select name from sqlite_master where type = 'table' and name = ?",
                [$table]
            );

            return !empty($result);
        }

        if ($driver === 'sqlsrv') {
            $result = $connection->select(
                'select [name] from sys.tables where [name] = ?',
                [$table]
            );

            return !empty($result);
        }

        throw $exception;
    }

    private function retryHasColumn(string $table, string $column, QueryException $exception): bool
    {
        $connection = Schema::getConnection();
        $driver     = $connection->getDriverName();

        if ($driver === 'sqlite') {
            $escapedTable = str_replace("'", "''", $table);
            $result       = $connection->select(
                "pragma table_info('{$escapedTable}')"
            );

            foreach ($result as $definition) {
                if (($definition->name ?? null) === $column) {
                    return true;
                }
            }

            return false;
        }

        if ($driver === 'sqlsrv') {
            $result = $connection->select(
                'select [name] from sys.columns where [object_id] = object_id(?) and [name] = ?',
                [$table, $column]
            );

            return !empty($result);
        }

        throw $exception;
    }


    private function ensurePdoDriver(): void
    {
        $connectionName = config('database.default');
        $driver         = config("database.connections.{$connectionName}.driver");

        if (!$driver) {
            return;
        }

        $extension = [
            'mysql'  => 'pdo_mysql',
            'pgsql'  => 'pdo_pgsql',
            'sqlsrv' => 'pdo_sqlsrv',
            'sqlite' => 'pdo_sqlite',
        ][$driver] ?? null;

        if ($extension && !extension_loaded($extension)) {
            throw new \RuntimeException(
                sprintf(
                    'La conexión "%s" requiere la extensión PHP "%s". Instálala y vuelve a ejecutar php artisan migrate. '
                    . 'Consulta docs/migration-guide.md para ver los paquetes recomendados por sistema operativo.',
                    $driver,
                    $extension
                )
            );
        }
    }

};
