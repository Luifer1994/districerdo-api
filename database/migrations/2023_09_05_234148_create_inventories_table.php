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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->float('quantity',12,2)->comment('Quantity of product');
            $table->foreignId('product_id')->constrained('products')->comment('Relation with products table');
            $table->foreignId('batch_id')->constrained('batches')->comment('Relation with batches table');
            $table->foreignId('user_id')->constrained('users')->comment('Relation with users table');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventories');
    }
};
