<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('menu_items', function (Blueprint $table) {
        $table->unsignedBigInteger('restaurant_id')->nullable()->after('disponible');
        // Si tienes tabla restaurants, añade la FK:
        // $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('menu_items', function (Blueprint $table) {
        $table->dropColumn('restaurant_id');
    });
}

};
