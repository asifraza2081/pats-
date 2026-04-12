<?php

namespace Database\Factories;

use App\Models\FinancialLedger;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialLedgerFactory extends Factory
{
    protected $model = FinancialLedger::class;

    public function definition(): array
    {
        return [
            'type' => 'expense',
            'source_type' => 'App\Models\Expense',
            'source_id' => $this->faker->randomNumber(),
            'project_id' => Project::factory(),
            'category' => 'Test Category',
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'tax_amount' => 0,
            'net_amount' => function (array $attributes) {
                return $attributes['amount'];
            },
            'ledger_date' => now()->toDateString(),
            'fiscal_year' => '2025-26',
            'created_by' => User::factory(),
        ];
    }
}
