<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialLedger;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class HardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function financial_ledger_entries_are_immutable_at_model_level()
    {
        $ledger = FinancialLedger::factory()->create([
            'amount' => 1000
        ]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Financial ledger entries are immutable and cannot be updated.');

        $ledger->update(['amount' => 2000]);
    }

    /** @test */
    public function financial_ledger_entries_cannot_be_deleted()
    {
        $ledger = FinancialLedger::factory()->create();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Financial ledger entries are immutable and cannot be deleted.');

        $ledger->delete();
    }

    /** @test */
    public function updating_an_expense_triggers_a_reversing_entry_pattern()
    {
        $admin = User::permission('edit expenses')->first();
        $this->actingAs($admin);

        $category = ExpenseCategory::first();
        $project = Project::first();

        // 1. Create initial expense
        $expense = Expense::create([
            'project_id' => $project->id,
            'category_id' => $category->id,
            'description' => 'Original Expense',
            'gross_amount' => 1000,
            'tax_amount' => 0,
            'net_amount' => 1000,
            'expense_date' => now()->toDateString(),
            'created_by' => $admin->id,
        ]);

        // Manually trigger the ledger post (simulating the controller logic)
        $this->postLedgerViaControllerLogic($expense);

        $this->assertEquals(1, FinancialLedger::where('source_id', $expense->id)->count());

        // 2. Update the expense
        $expense->update(['gross_amount' => 1500, 'net_amount' => 1500]);
        
        // Trigger update logic
        $this->postLedgerViaControllerLogic($expense);

        // 3. Verify Ledger State
        // Should have 3 entries: 
        // 1. Original (1000)
        // 2. Reversal (-1000)
        // 3. New Corrected (1500)
        $entries = FinancialLedger::where('source_id', $expense->id)->orderBy('id')->get();
        
        $this->assertCount(3, $entries);
        $this->assertEquals(1000, $entries[0]->amount);
        $this->assertEquals(-1000, $entries[1]->amount);
        $this->assertStringContainsString('REVERSAL', $entries[1]->description);
        $this->assertEquals(1500, $entries[2]->amount);
    }

    /** @test */
    public function notification_mark_all_read_returns_json()
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.notifications.mark-all-as-read'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    private function postLedgerViaControllerLogic($expense)
    {
        // We simulate the private method logic here or call a helper
        // Since it's a feature test, we could just call the controller route, 
        // but for unit-purity on the pattern:
        $app = app();
        $controller = $app->make(\App\Http\Controllers\Admin\ExpenseController::class);
        
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('updateLedger');
        $method->setAccessible(true);
        $method->invokeArgs($controller, [$expense]);
    }
}
