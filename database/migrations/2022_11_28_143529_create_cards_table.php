<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');            
            $table->string('address_id')->nullable();
            $table->string('card_id')->nullable();
            $table->string('first_six_digits')->nullable();
            $table->string('last_four_digits')->nullable();
            $table->string('brand')->nullable();
            $table->string('holder_name')->nullable();
            $table->string('holder_document')->nullable();
            $table->string('exp_month')->nullable();
            $table->string('exp_year')->nullable();
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->string('label')->nullable();
            $table->string('card_token')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cards');
    }
};
