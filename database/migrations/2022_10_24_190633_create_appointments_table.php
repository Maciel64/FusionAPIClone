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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('patient_name')->nullable();
            $table->string('patient_phone')->nullable();
            $table->foreignId('schedule_id')->constrained();
            $table->foreignId('customer_id')->constrained('users', 'id')->onDelete('cascade');
            $table->foreignId('room_id')->constrained();
            $table->dateTime('time_init');
            $table->dateTime('time_end');
            $table->integer('time_total');
            $table->string('status');
            $table->decimal('value_per_minute', 8, 2);
            $table->decimal('value_total', 8, 2);
            $table->timestamp('checkin_at')->nullable();
            $table->timestamp('checkout_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
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
        Schema::dropIfExists('appointments');
    }
};
