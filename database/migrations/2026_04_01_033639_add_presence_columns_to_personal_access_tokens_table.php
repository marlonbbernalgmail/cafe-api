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

    public function up(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->timestamp('presence_pinged_at')->nullable()->after('last_used_at');
            $table->string('presence_app_version')->nullable()->after('presence_pinged_at');
            $table->string('presence_device_id')->nullable()->after('presence_app_version');
            $table->string('presence_platform')->nullable()->after('presence_device_id');
        });
    }

    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn(['presence_pinged_at', 'presence_app_version', 'presence_device_id', 'presence_platform']);
        });
    }
};
