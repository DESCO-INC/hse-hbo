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
            $table->text('SWA')->nullable()->after('verified_remarks');
            $table->text('SRO')->nullable()->after('SWA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hbo_list', function (Blueprint $table) {
            //
        });
    }
};
