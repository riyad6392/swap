<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanAndPlanDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plans')->insert([
            'uid' => uniqid(),
            'name' => 'Basic Plan',
            'description' => 'This is a basic plan',
            'amount' => 10,
            'currency' => 'USD',
            'interval' => 'month',
            'stripe_price_id' => 'price_1P9mkCIZ9zy7k2DvUnvuBlhb',
            'short_description' => 'Basic Plan',
            'is_active' => 1,
            'plan_type' => 'basic',
            'interval_duration' => 1,
            'is_super_swapper' => 0,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (range(1, 10) as $index) {
            DB::table('plan_details')->insert([
                'plan_id' => 1,
                'feature' => fake()->sentence(4),
                'features_count' => 2,
                'value' => 'Value 2',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('plans')->insert([
            'uid' => uniqid(),

            'name' => 'Premium Plan',
            'description' => 'This is a Premium plan',
            'amount' => 69.99,
            'currency' => 'USD',
            'interval' => 'year',
            'stripe_price_id' => 'price_1P9mnTIZ9zy7k2DvARlafmH9',
            'short_description' => 'Premium Plan',
            'is_active' => 1,
            'plan_type' => 'premium',
            'interval_duration' => 1,
            'is_super_swapper' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (range(1, 10) as $index) {
            DB::table('plan_details')->insert([
                'plan_id' => 2,
                'feature' => fake()->sentence(4),
                'features_count' => 2,
                'value' => 'Value 2',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
