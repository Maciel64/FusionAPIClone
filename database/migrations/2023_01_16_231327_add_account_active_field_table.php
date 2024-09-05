<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
      Schema::table('users', function (Blueprint $table) {
        $table->boolean('account_active')->default(true);
        $table->timestamp('account_activated_at')->nullable();
        $table->timestamp('account_deactivated_at')->nullable();
      });
    }

    public function down()
    {
      Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('account_active');
        $table->dropColumn('account_activated_at');
        $table->dropColumn('account_deactivated_at');
      });
    }
};
