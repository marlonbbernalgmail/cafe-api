<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function getConnection(): ?string
    {
        return config('authentication.users_connection');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_presences', function (Blueprint $table) {
            $table->string('device_id')->nullable()->after('last_seen_at');
            $table->string('platform', 50)->nullable()->after('device_id');
            $table->string('app_version', 50)->nullable()->after('platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_presences', function (Blueprint $table) {
            $table->dropColumn([
                'device_id',
                'platform',
                'app_version',
            ]);
        });
    }
};
