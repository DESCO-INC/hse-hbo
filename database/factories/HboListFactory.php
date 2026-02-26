<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HboList>
 */
class HboListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_unit' => $this->faker->randomElement(['PIONEER1']),
            'company' => $this->faker->randomElement([
                'PGPC', 'DESCO', 'Soliman', 'IMPIC', 'MILEAGE', 'SDI',
                'RYT', 'T1/RCCe', 'JCC', 'UZMA', 'SLB', 'CSA',
                'WEATHERFORD', 'ADA', 'PGEI', 'PAMPISCO', 'OTHERS'
            ]),
            'type' => $this->faker->randomElement([
                'Safe Behavior', 'Safe Condition', 'Unsafe Behavior', 'Unsafe Condition'
            ]),
            'category' => $this->faker->word(),
            'sub_category' => $this->faker->word(),
            'hazard_description' => $this->faker->sentence(),
            'recommendation' => $this->faker->sentence(),
            'reported_by' => $this->faker->name(),
            'reported_to' => $this->faker->name(),
            'date_raised' => $this->faker->dateTimeBetween(now()->startOfYear(), now())->format('Y-m-d'),
            'date_due' => $this->faker->dateTimeBetween(now()->startOfYear(), now())->format('Y-m-d'),

            // Leave action fields blank
            'action_by' => null,
            'action_date' => null,
            'action_remarks' => null,

            // Leave verified fields blank
            'verified_by' => null,
            'verified_date' => null,
            'verified_remarks' => null,

            // Set status to only ONGOING
            'status' => 'ONGOING',

            'created_by' => $this->faker->name(),
        ];
    }
}