<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('approved');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->string('approved')->default('awaiting_review')->after('payer_email');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('approved');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('approved')->default(false)->after('payer_email');
        });
    }
}; 