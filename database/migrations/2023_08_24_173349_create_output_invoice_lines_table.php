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
        Schema::create('output_invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('output_id')->constrained('outputs')->comment('Relation with outputs table');
            $table->foreignId('invoice_line_id')->constrained('invoice_lines')->comment('Relation with invoice_lines table');
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
        Schema::dropIfExists('output_invoice_lines');
    }
};
