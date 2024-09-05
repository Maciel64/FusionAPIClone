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
        Schema::create('pagar_me_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->nullable();
            $table->string('logged_user_name');
            $table->string('action');
            $table->text('request');
            $table->text('response');            
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
        Schema::dropIfExists('pagar_me_logs');
    }
};
