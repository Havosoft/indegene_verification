<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('nin_verification_status', ['pending', 'verified', 'failed'])->default('pending')->after('is_verified');
            $table->text('nin_verification_failure_reason')->nullable()->after('nin_verification_status');
            $table->json('nin_verification_data')->nullable()->after('nin_verification_failure_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nin_verification_status', 'nin_verification_failure_reason', 'nin_verification_data']);
        });
    }
};