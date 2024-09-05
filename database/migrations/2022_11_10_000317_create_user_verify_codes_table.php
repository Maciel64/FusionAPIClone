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
        Schema::create('user_verify_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();            
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->integer('code');
            $table->boolean('expired')->nullable()->default(false);
            $table->timestamp('expires_at')->nullable()->default(null);
            $table->timestamp('verified_at')->nullable()->default(null);
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
        Schema::dropIfExists('user_verify_codes');
    }
};
