<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FinancialCategory;
use App\Models\FinancialLedger;
use App\Models\FinancialSetting;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Expense::with(['category', 'project', 'creator'])->latest('expense_date');

        if ($request->filled('project_id'))   $query->where('project_id', $request->project_id);
        if ($request->filled('category_id'))  $query->where('category_id', $request->category_id);
        if ($request->filled('from'))         $query->where('expense_date', '>=', $request->from);
        if ($request->filled('to'))           $query->where('expense_date', '<=', $request->to);

        $expenses   = $query->paginate(20)->withQueryString();
        $totals     = Expense::when($request->filled('project_id'), fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->filled('from'), fn($q) => $q->where('expense_date', '>=', $request->from))
            ->when($request->filled('to'),   fn($q) => $q->where('expense_date', '<=', $request->to))
            ->selectRaw('SUM(gross_amount) as gross, SUM(tax_amount) as tax, SUM(net_amount) as net')
            ->first();

        $projects   = Project::orderBy('name')->get(['id', 'name']);
        $categories = FinancialCategory::expense()->orderBy('name')->get();

        return view('admin.expenses.index', compact('expenses', 'totals', 'projects', 'categories'));
    }

    // ── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $categories = FinancialCategory::expense()->orderBy('name')->get();
        $projects   = Project::orderBy('name')->get(['id', 'name']);
        $defaultGst = FinancialSetting::defaultGstRate();
        $fbrMode    = FinancialSetting::fbrModeEnabled();

        return view('admin.expenses.create', compact('categories', 'projects', 'defaultGst', 'fbrMode'));
    }

    // ── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'     => 'nullable|exists:projects,id',
            'category_id'    => 'required|exists:financial_categories,id',
            'expense_date'   => 'required|date',
            'voucher_no'     => 'nullable|string|max:50',
            'description'    => 'required|string|max:500',
            'recipient_name' => 'nullable|string|max:150',
            'recipient_ntn'  => 'nullable|string|max:20',
            'recipient_cnic' => 'nullable|string|max:20',
            'gross_amount'   => 'required|numeric|min:0',
            'tax_rate'       => 'required|numeric|min:0|max:50',
            'attachment'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $taxFields = Expense::computeTaxFields((float)$data['gross_amount'], (float)$data['tax_rate']);
        $data = array_merge($data, $taxFields, ['created_by' => Auth::id()]);

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('expense-vouchers', 'public');
        }

        unset($data['attachment']);
        $expense = Expense::create($data);

        // Post to ledger
        $this->postToLedger($expense);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense recorded and posted to ledger.');
    }

    // ── Edit ─────────────────────────────────────────────────────────────────

    public function edit(Expense $expense)
    {
        $categories = FinancialCategory::expense()->orderBy('name')->get();
        $projects   = Project::orderBy('name')->get(['id', 'name']);
        $defaultGst = FinancialSetting::defaultGstRate();
        $fbrMode    = FinancialSetting::fbrModeEnabled();

        return view('admin.expenses.edit', compact('expense', 'categories', 'projects', 'defaultGst', 'fbrMode'));
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'project_id'     => 'nullable|exists:projects,id',
            'category_id'    => 'required|exists:financial_categories,id',
            'expense_date'   => 'required|date',
            'voucher_no'     => 'nullable|string|max:50',
            'description'    => 'required|string|max:500',
            'recipient_name' => 'nullable|string|max:150',
            'recipient_ntn'  => 'nullable|string|max:20',
            'recipient_cnic' => 'nullable|string|max:20',
            'gross_amount'   => 'required|numeric|min:0',
            'tax_rate'       => 'required|numeric|min:0|max:50',
            'attachment'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $taxFields = Expense::computeTaxFields((float)$data['gross_amount'], (float)$data['tax_rate']);
        $data = array_merge($data, $taxFields);

        if ($request->hasFile('attachment')) {
            if ($expense->attachment_path) Storage::disk('public')->delete($expense->attachment_path);
            $data['attachment_path'] = $request->file('attachment')->store('expense-vouchers', 'public');
        }

        unset($data['attachment']);
        $expense->update($data);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    // ── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Expense $expense)
    {
        // Note: ledger entry remains (immutable) for audit trail
        $expense->delete();
        return back()->with('success', 'Expense deleted. The ledger entry is preserved for audit.');
    }

    // ── Print Voucher (PDF) ──────────────────────────────────────────────────

    public function printVoucher(Expense $expense)
    {
        $expense->load(['project', 'category', 'creator']);
        $org = [
            'name'    => FinancialSetting::get('org_name_for_tax', 'PATS'),
            'ntn'     => FinancialSetting::get('org_ntn', ''),
            'address' => FinancialSetting::get('org_address_for_tax', ''),
        ];

        $pdf = Pdf::loadView('pdf.financial.expense-voucher', compact('expense', 'org'))
            ->setPaper('a5', 'portrait');

        return $pdf->stream("expense-voucher-{$expense->id}.pdf");
    }

    // ── Private: Post Expense to Ledger ──────────────────────────────────────

    private function postToLedger(Expense $expense): void
    {
        $carbonDate = \Carbon\Carbon::parse($expense->expense_date);
        $fyStart    = FinancialSetting::fyStartMonth();

        FinancialLedger::create([
            'type'        => 'expense',
            'source_type' => Expense::class,
            'source_id'   => $expense->id,
            'project_id'  => $expense->project_id,
            'category'    => $expense->category->name ?? 'Uncategorized',
            'description' => $expense->description,
            'amount'      => $expense->gross_amount,
            'tax_amount'  => $expense->tax_amount,
            'net_amount'  => $expense->net_amount,
            'ledger_date' => $expense->expense_date,
            'fiscal_year' => FinancialLedger::fiscalYearFor($carbonDate, $fyStart),
            'created_by'  => Auth::id(),
        ]);
    }
}
