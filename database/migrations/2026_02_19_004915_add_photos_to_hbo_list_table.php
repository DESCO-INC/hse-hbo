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
        Schema::table('hbo_list', function (Blueprint $table) {
            $table->json('hbo_photo')->nullable()->after('status');
            $table->json('action_photo')->nullable()->after('hbo_photo');
            $table->json('verification_photo')->nullable()->after('action_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hbo_list', function (Blueprint $table) {
            $table->dropColumn([
                'hbo_photo',
                'action_photo',
                'verification_photo'
            ]);
        });
    }
};
