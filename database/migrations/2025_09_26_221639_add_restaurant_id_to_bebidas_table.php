<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('bebidas', 'restaurant_id')) {
            Schema::table('bebidas', function (Blueprint $table) {
                $table->unsignedBigInteger('restaurant_id')->nullable()->after('id');
            });
        }

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

        DB::statement('ALTER TABLE "bebidas" ALTER COLUMN "restaurant_id" SET NOT NULL');

        $fk = 'bebidas_restaurant_id_foreign';
        $exists = DB::selectOne("
            SELECT 1 FROM information_schema.table_constraints
            WHERE constraint_type='FOREIGN KEY' AND table_name='bebidas' AND constraint_name='{$fk}'
        ");
        if (!$exists) {
            Schema::table('bebidas', function (Blueprint $table) use ($fk) {
                $table->foreign('restaurant_id', $fk)
                      ->references('id')->on('restaurants')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bebidas', 'restaurant_id')) {
            Schema::table('bebidas', function (Blueprint $table) {
                $table->dropForeign('bebidas_restaurant_id_foreign');
                $table->dropColumn('restaurant_id');
            });
        }
    }
};
