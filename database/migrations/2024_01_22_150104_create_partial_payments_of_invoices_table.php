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
        Schema::create('partial_payments_of_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->comment('Relation with invoices table');
            $table->float('amount', 12, 2)->comment('Amount of partial payment');
            $table->string('evidence')->nullable()->comment('Evidence of partial payment');
            $table->string('description')->nullable()->comment('Description of partial payment');
            $table->foreignId('user_id')->constrained('users')->comment('User creator the partial payment');
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
        Schema::dropIfExists('partial_payments_of_invoices');
    }
};
