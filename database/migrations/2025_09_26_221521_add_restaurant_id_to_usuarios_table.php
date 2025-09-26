<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('usuarios', 'restaurant_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->unsignedBigInteger('restaurant_id')->nullable()->after('id');
            });
        }

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
            DB::table('usuarios')->whereNull('restaurant_id')->update(['restaurant_id' => $rid]);
        });

        DB::statement('ALTER TABLE "usuarios" ALTER COLUMN "restaurant_id" SET NOT NULL');

        $fk = 'usuarios_restaurant_id_foreign';
        $exists = DB::selectOne("
            SELECT 1 FROM information_schema.table_constraints
            WHERE constraint_type='FOREIGN KEY' AND table_name='usuarios' AND constraint_name='{$fk}'
        ");
        if (!$exists) {
            Schema::table('usuarios', function (Blueprint $table) use ($fk) {
                $table->foreign('restaurant_id', $fk)
                      ->references('id')->on('restaurants')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('usuarios', 'restaurant_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropForeign('usuarios_restaurant_id_foreign');
                $table->dropColumn('restaurant_id');
            });
        }
    }
};
