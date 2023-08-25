<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name of the user');
            $table->string('last_name')->comment('Last name of the user');
            $table->foreignId('document_type_id')->constrained('document_types')->comment('Document type of the user');
            $table->string('document',20)->comment('Document of the user');
            $table->string('email')->unique()->comment('Email of the user');
            $table->boolean('is_active')->default(true)->comment('Is the user active?');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->comment('Password of the user');
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('changed_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('When the user was changed?');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
