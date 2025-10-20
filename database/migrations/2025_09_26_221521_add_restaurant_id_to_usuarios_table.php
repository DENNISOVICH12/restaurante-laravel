<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'restaurant_id')) {
                $table->unsignedBigInteger('restaurant_id')->nullable()->after('id');
                $table->foreign('restaurant_id')->references('id')->on('restaurants')->cascadeOnDelete();
            }
        });

        // Aseguramos que la tabla bebidas existe
        if (!Schema::hasTable('bebidas')) {
            Schema::create('bebidas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->decimal('precio', 10, 2)->default(0);
                $table->boolean('disponible')->default(true);
                $table->timestamps();
            });
        }

        // Agregamos la columna restaurant_id si no existe
        Schema::table('bebidas', function (Blueprint $table) {
            if (!Schema::hasColumn('bebidas', 'restaurant_id')) {
                $table->foreignId('restaurant_id')
                    ->nullable() // se deja nullable para evitar errores en SQLite
                    ->constrained('restaurants')
                    ->cascadeOnDelete();
            }
        });

        // Creamos un restaurante por defecto si no existe
        DB::transaction(function () {
            $rid = DB::table('restaurants')->value('id');
            if (!$rid) {
                $rid = DB::table('restaurants')->insertGetId([
                    'nombre' => 'Restaurante Default',
                    'slug'   => 'default',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Asignamos ese restaurante a las bebidas existentes
            DB::table('bebidas')->whereNull('restaurant_id')->update(['restaurant_id' => $rid]);
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('bebidas') && Schema::hasColumn('bebidas', 'restaurant_id')) {
            Schema::table('bebidas', function (Blueprint $table) {
                $table->dropForeign(['restaurant_id']);
                $table->dropColumn('restaurant_id');
            });
        }
        Schema::table('usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('usuarios', 'restaurant_id')) {
                $table->dropForeign(['restaurant_id']);
                $table->dropColumn('restaurant_id');
            }
        });
    }
};
