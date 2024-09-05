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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('model_type');
            $table->string('model_id');
            $table->string('line_1', 180);
            $table->string('line_2')->nullable();
            $table->string('city', 180);
            $table->string('state', 2);
            $table->string('country');
            $table->string('neighborhood')->nullable();
            $table->string('zip_code', 10);
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
        Schema::dropIfExists('addresses');
    }
};
