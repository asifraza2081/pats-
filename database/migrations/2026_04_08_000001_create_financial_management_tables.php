<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── 1. FINANCIAL CATEGORIES ──────────────────────────────────────────
        Schema::create('financial_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('type', ['revenue', 'expense']);
            $table->string('description', 255)->nullable();
            $table->boolean('is_system')->default(false)->comment('System categories cannot be deleted');
            $table->timestamps();
        });

        // ── 2. EXPENSES ──────────────────────────────────────────────────────
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('category_id')->constrained('financial_categories');
            $table->foreignId('created_by')->constrained('users');
            $table->date('expense_date');
            $table->string('voucher_no', 50)->nullable()->comment('Internal voucher/PO number');
            $table->string('description', 500);
            $table->string('recipient_name', 150)->nullable()->comment('Payee name');
            $table->string('recipient_ntn', 20)->nullable()->comment('Payee NTN — for FBR Annex-A');
            $table->string('recipient_cnic', 20)->nullable()->comment('Payee CNIC if no NTN');
            $table->decimal('gross_amount', 12, 2)->comment('Amount before tax deduction');
            $table->decimal('tax_rate', 5, 2)->default(0.00)->comment('Withholding tax rate %');
            $table->decimal('tax_amount', 10, 2)->default(0.00)->comment('Computed tax deducted');
            $table->decimal('net_amount', 12, 2)->comment('Net payable = gross - tax');
            $table->string('attachment_path', 500)->nullable()->comment('Scanned voucher/receipt');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['expense_date', 'project_id']);
        });

        // ── 3. FINANCIAL LEDGER (Immutable Audit Trail) ──────────────────────
        Schema::create('financial_ledger', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['revenue', 'expense']);
            $table->string('source_type', 100)->nullable()->comment('Morph: App\\Models\\Payment or App\\Models\\Expense');
            $table->unsignedBigInteger('source_id')->nullable()->comment('FK to source record');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('category', 120)->comment('Category snapshot at time of posting');
            $table->string('description', 500);
            $table->decimal('amount', 12, 2)->comment('Gross amount (always positive)');
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('net_amount', 12, 2)->comment('Amount net of tax');
            $table->date('ledger_date')->comment('Effective date for tax reporting');
            $table->string('fiscal_year', 10)->comment('e.g. 2024-25 (July-June)');
            $table->foreignId('created_by')->constrained('users');
            // NO softDeletes — immutable for audit integrity
            $table->timestamps();

            $table->index(['fiscal_year', 'type']);
            $table->index(['project_id', 'ledger_date']);
            $table->index(['source_type', 'source_id']);
        });

        // ── 4. FINANCIAL SETTINGS (Key-Value Store) ──────────────────────────
        Schema::create('financial_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->text('value')->nullable();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_settings');
        Schema::dropIfExists('financial_ledger');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('financial_categories');
    }
};
