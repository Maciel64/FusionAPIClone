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
      Schema::create('failed_charges', function (Blueprint $table) {
        $table->id();
        $table->uuid('uuid');
        $table->foreignId('user_id')->constrained('users');
        $table->integer('year_reference');
        $table->integer('month_reference');
        $table->dateTime('failed_at');
        $table->integer('attempts')->default(0);
        $table->softDeletes();
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
      Schema::dropIfExists('failed_charges');
    }
};
