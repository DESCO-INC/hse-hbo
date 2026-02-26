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
        Schema::create('hbo_list', function (Blueprint $table) {
            $table->id();
            $table->string('business_unit')->nullable();
            $table->string('company')->nullable();
            $table->string('type')->nullable();
            $table->string('category')->nullable();
            $table->string('sub_category')->nullable();
            $table->text('hazard_description')->nullable();
            $table->text('recommendation')->nullable();
            $table->string('reported_by')->nullable();
            $table->string('reported_to')->nullable();
            $table->date('date_raised')->nullable();
            $table->date('date_due')->nullable();
            $table->string('action_by')->nullable();
            $table->date('action_date')->nullable();
            $table->text('action_remarks')->nullable();
            $table->string('verified_by')->nullable();
            $table->date('verified_date')->nullable();
            $table->text('verified_remarks')->nullable();
            $table->text('SWA')->nullable();
            $table->text('SRO')->nullable();
            $table->string('status')->nullable();
            $table->json('hbo_photo')->nullable();
            $table->json('action_photo')->nullable();
            $table->json('verification_photo')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hbo_list');
    }
};
