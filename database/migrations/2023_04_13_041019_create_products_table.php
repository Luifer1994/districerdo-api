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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name',100)->comment('Name of product');
            $table->string('description',255)->nullable()->comment('Description of product');
            $table->char('sku',10)->unique()->comment('SKU of product');
            $table->foreignId('category_id')->constrained('categories')->comment('Relation with categories table');
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
        Schema::dropIfExists('products');
    }
};
