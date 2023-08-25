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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Name of the client');
            $table->string('last_name', 100)->comment('Last name of the client');
            $table->string('email', 100)->nullable()->comment('Email of the client');
            $table->string('phone', 100)->nullable()->comment('Phone of the client');
            $table->string('document_number', 20)->comment('Document number of the client');
            $table->string('address', 120)->nullable()->comment('Address of the client');
            $table->foreignId('document_type_id')->constrained('document_types')->comment('Document type of the client');
            $table->foreignId('city_id')->constrained('cities')->comment('City of the client');
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
        Schema::dropIfExists('clients');
    }
};
