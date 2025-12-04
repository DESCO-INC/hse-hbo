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
        Schema::create('pob_records', function (Blueprint $table) {
            $table->id();
            $table->string('business_unit');
            $table->date('date'); // proper DATE type
            $table->json('attendance_data'); // store JSON of company => attendance
            $table->timestamps();

            // make business_unit + date combination unique
            $table->unique(['business_unit', 'date'], 'unique_pob_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pob_records');
    }
};
