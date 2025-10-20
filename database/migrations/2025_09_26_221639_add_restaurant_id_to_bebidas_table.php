<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1️⃣ Agregar columna restaurant_id si no existe
        if (!Schema::hasColumn('bebidas', 'restaurant_id')) {
            Schema::table('bebidas', function (Blueprint $table) {
                $table->unsignedBigInteger('restaurant_id')->nullable()->after('id');
            });
        }

        // 2️⃣ Crear restaurante por defecto y asignarlo a las bebidas existentes
        DB::transaction(function () {
            $rid = DB::table('restaurants')->value('id') ??
                DB::table('restaurants')->insertGetId([
                    'nombre' => 'Restaurante Default',
                    'slug'   => 'default',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            DB::table('bebidas')->whereNull('restaurant_id')->update(['restaurant_id' => $rid]);
        });

        // 3️⃣ Crear la foreign key solo si NO estamos en SQLite
        if (DB::getDriverName() !== 'sqlite') {
            $fk = 'bebidas_restaurant_id_foreign';
            $exists = DB::selectOne("
                SELECT 1 FROM information_schema.table_constraints
                WHERE constraint_type='FOREIGN KEY'
                AND table_name='bebidas'
                AND constraint_name='{$fk}'
            ");

            if (!$exists) {
                Schema::table('bebidas', function (Blueprint $table) use ($fk) {
                    $table->foreign('restaurant_id', $fk)
                        ->references('id')->on('restaurants')
                        ->cascadeOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bebidas', 'restaurant_id')) {
            Schema::table('bebidas', function (Blueprint $table) {
                if (Schema::hasColumn('bebidas', 'restaurant_id')) {
                    $table->dropForeign(['restaurant_id']);
                    $table->dropColumn('restaurant_id');
                }
            });
        }
    }
};
