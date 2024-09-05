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
        Schema::create('coworking_opening_hours', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();     
            $table->foreignId('coworking_id')->constrained('coworkings')->cascadeOnDelete();
            $table->string('day_of_week');
            $table->time('opening');
            $table->time('closing');
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
        Schema::dropIfExists('coworking_opening_hours');
    }
};
