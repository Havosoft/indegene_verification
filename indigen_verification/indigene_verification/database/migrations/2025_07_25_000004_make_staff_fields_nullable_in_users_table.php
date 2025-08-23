<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('place_of_birth')->nullable()->change();
            $table->string('nin')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->string('place_of_birth')->nullable(false)->change();
            $table->string('nin')->nullable(false)->change();
        });
    }
}; 