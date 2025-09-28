<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('clientes')) {
            return;
        }

        if (Schema::hasColumn('clientes', 'nombre') && !Schema::hasColumn('clientes', 'nombre_cliente')) {
            DB::statement("ALTER TABLE clientes CHANGE nombre nombre_cliente VARCHAR(255) NOT NULL");
        }

        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'telefono')) {
                $table->string('telefono', 50)->nullable();
            }

            if (!Schema::hasColumn('clientes', 'direccion')) {
                $table->string('direccion', 255)->nullable();
            }

            if (!Schema::hasColumn('clientes', 'fecha_registro')) {
                $table->timestamp('fecha_registro')->nullable()->useCurrent();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('clientes')) {
            return;
        }

        if (Schema::hasColumn('clientes', 'nombre_cliente') && !Schema::hasColumn('clientes', 'nombre')) {
            DB::statement("ALTER TABLE clientes CHANGE nombre_cliente nombre VARCHAR(255) NOT NULL");
        }

        if (Schema::hasColumn('clientes', 'telefono')) {
            DB::statement('ALTER TABLE clientes DROP COLUMN telefono');
        }

        if (Schema::hasColumn('clientes', 'direccion')) {
            DB::statement('ALTER TABLE clientes DROP COLUMN direccion');
        }

        if (Schema::hasColumn('clientes', 'fecha_registro')) {
            DB::statement('ALTER TABLE clientes DROP COLUMN fecha_registro');
        }
    }
};
