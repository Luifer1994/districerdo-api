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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->char('code',8)->unique()->comment('Code the invoice');
            $table->enum('state',['CANCELLED','PAID','PENDING'])->default('PENDING')->comment('States for invoice');
            $table->text('observation')->nullable()->comment('Obervations or comments to invoice');
            $table->foreignId('client_id')->constrained('clients')->comment('Relation with clients table');
            $table->foreignId('user_id')->constrained('users')->comment('User creator the invoice');
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
        Schema::dropIfExists('invoices');
    }
};
