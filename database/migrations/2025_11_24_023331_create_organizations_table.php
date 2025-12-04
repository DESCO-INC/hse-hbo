<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('business_unit');
            $table->string('company_name');
            $table->timestamps();
        });

        // Insert initial data
        $companies = [
            'PGPC',
            'DESCO',
            'Soliman',
            'IMPIC',
            'MILEAGE',
            'SDI',
            'RYT',
            'T1/RCCe',
            'JCC',
            'UZMA',
            'SLB',
            'CSA',
            'WEATHERFORD',
            'ADA',
            'PGEI',
            'PAMPISCO',
            'OTHERS',
        ];

        foreach ($companies as $company) {
            DB::table('organizations')->insert([
                'business_unit' => 'PIONEER1',
                'company_name' => $company,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
