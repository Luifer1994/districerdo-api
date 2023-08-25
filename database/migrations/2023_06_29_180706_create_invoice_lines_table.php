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
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->float('price', 12, 2)->comment('Pirce to service');
            $table->float('quantity', 12, 2)->comment('Quantity of service');
            $table->foreignId('invoice_id')->constrained('invoices')->comment('Relation with invoices table');
            $table->foreignId('product_id')->constrained('products')->comment('Relation with products table');
            $table->foreignId('batch_id')->constrained('batches')->comment('Relation with batches table');
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
        Schema::dropIfExists('invoice_lines');
    }
};
