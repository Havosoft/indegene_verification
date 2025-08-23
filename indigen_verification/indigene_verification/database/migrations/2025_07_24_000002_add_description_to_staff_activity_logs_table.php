<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('staff_activity_logs', function (Blueprint $table) {
            $table->text('description')->nullable()->after('details');
        });
    }

    public function down()
    {
        Schema::table('staff_activity_logs', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}; 