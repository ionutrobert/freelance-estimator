<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('hourly_rate', 8, 2)->default(45.00)->after('email');
            $table->string('currency', 3)->default('USD')->after('hourly_rate');
            $table->string('avatar')->nullable()->after('currency'); // For social auth
            $table->string('google_id')->nullable()->after('avatar');
            $table->string('github_id')->nullable()->after('google_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['hourly_rate', 'currency', 'avatar', 'google_id', 'github_id']);
        });
    }
};
