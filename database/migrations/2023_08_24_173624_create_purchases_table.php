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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->char('code', 4)->unique()->comment('Code of the purchase');
            $table->foreignId('provider_id')->constrained('providers')->comment('Relation with providers table');
            $table->foreignId('user_id')->constrained()->comment('Relation with users table');
            $table->enum('status', ['PENDING', 'PAID'])->default('PENDING')->comment('Status of the purchase');
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
        Schema::dropIfExists('purchases');
    }
};
