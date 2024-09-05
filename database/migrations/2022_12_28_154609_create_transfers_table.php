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
      Schema::create('transfers', function (Blueprint $table) {
        $table->id();
        $table->uuid('uuid');
        $table->foreignId('partner_id')->constrained('users')->cascadeOnDelete();
        $table->string('order_id');
        $table->string('status')->default('pending');
        $table->text('note')->nullable();
        $table->float('amount', 8, 2);
        $table->float('discount', 8, 2)->default(0);
        $table->float('total', 8, 2)->default(0);
        $table->string('receipt_name')->nullable();
        $table->string('receipt_url')->nullable();
        $table->string('updated_by')->nullable();
        $table->timestamp('paid_at')->nullable();
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
        Schema::dropIfExists('transfers');
    }
};
