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
        Schema::create('plan_has_users', function (Blueprint $table) {
          $table->id();
          $table->uuid('uuid');
          $table->foreignId('plan_id')->constrained('plans');
          $table->foreignId('user_id')->constrained('users');
          $table->dateTime('start_date');
          $table->dateTime('end_date')->nullable();
          $table->boolean('active')->default(true);
          $table->boolean('trialing')->default(false);
          $table->dateTime('trial_start_date')->nullable();
          $table->dateTime('trial_end_date')->nullable();
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
        Schema::dropIfExists('plan_has_users');
    }
};
