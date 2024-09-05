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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('user_id')->constrained('users'); // customerId
            $table->morphs('model');
            $table->float('amount', 8, 2);
            $table->string('paid')->default('pending');
            $table->string('payment_method')->nullable();
            $table->dateTime('payment_at')->nullable();
            $table->string('order_id')->nullable();
            $table->string('order_code')->nullable();
            $table->boolean('closed')->default(false);
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
        Schema::dropIfExists('billings');
    }
};
