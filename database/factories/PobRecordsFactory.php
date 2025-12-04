<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PobRecords>
 */
class PobRecordsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companies = ['PGPC', 'DESCO', 'Soliman', 'IMPIC', 'MILEAGE', 'SDI', 'RYT', 'T1/RCCe', 'JCC', 'UZMA', 'SLB', 'CSA', 'WEATHERFORD', 'ADA', 'PGEI', 'PAMPISCO', 'OTHERS'];

        $units = ['PGPC', 'DESCO', 'Soliman', 'IMPIC', 'Mileage', 'SDI', 'RYT', 'T1/RCCe', 'JCC', 'UZMA', 'SLB', 'CSA', 'WEATHERFORD', 'ADA', 'PGEI', 'Pan-pisco', 'Others'];

        return [
            'business_unit' => $this->faker->randomElement($units),
            'company' => $this->faker->randomElement($companies),
            'attendance' => $this->faker->numberBetween(1, 20),
            'date' => $this->faker->date(), // example: "2025-11-05"
        ];
    }
}
