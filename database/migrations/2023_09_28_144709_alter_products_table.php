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
        Schema::table('products', function(Blueprint $table){
            $table->text('short_description')->nullable()->after('description');
            $table->text('shopping_returns')->nullable()->after('short_description');
            $table->text('related_products')->nullable()->after('shopping_returns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function(Blueprint $table){
            $table->dropColumn('short_description');
            $table->dropColumn('shopping_returns');
            $table->dropColumn('related_products');
        });
    }
};
