<?php

namespace App\Services;

use App\Models\FinancialLedger;
use App\Models\FinancialSetting;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinancialService
{
    // ── KPI Dashboard ────────────────────────────────────────────────────────

    public function getKPIs(?string $from = null, ?string $to = null, ?string $fiscalYear = null): array
    {
        $query = FinancialLedger::query();

        if ($fiscalYear) {
            $query->forFiscalYear($fiscalYear);
        } else {
            $query->dateRange($from, $to);
        }

        $revenue = (clone $query)->revenue()->selectRaw('SUM(amount) as total, SUM(tax_amount) as tax')->first();
        $expense = (clone $query)->expense()->selectRaw('SUM(amount) as total, SUM(tax_amount) as tax')->first();

        $totalRevenue  = (float) ($revenue->total ?? 0);
        $totalExpense  = (float) ($expense->total ?? 0);
        $taxCollected  = (float) ($revenue->tax ?? 0);
        $taxDeducted   = (float) ($expense->tax ?? 0);

        return [
            'total_revenue'  => $totalRevenue,
            'total_expense'  => $totalExpense,
            'net_surplus'    => $totalRevenue - $totalExpense,
            'tax_collected'  => $taxCollected,
            'tax_deducted'   => $taxDeducted,
            'net_tax'        => $taxDeducted,
        ];
    }

    // ── Monthly Trend (12 months of a given calendar year, split by FY) ─────

    public function getMonthlyTrend(string $fiscalYear): array
    {
        // FY e.g. "2024-25" → July 2024 – June 2025
        [$startYear, $endYearShort] = explode('-', $fiscalYear);
        $startYear = (int) $startYear;
        $endYear   = (int) ('20' . $endYearShort);

        $fyStart = FinancialSetting::fyStartMonth();

        $months   = [];
        $revenue  = array_fill(0, 12, 0.0);
        $expenses = array_fill(0, 12, 0.0);
        $labels   = [];

        for ($i = 0; $i < 12; $i++) {
            $month = Carbon::create($startYear)->startOfYear()->addMonths($fyStart - 1 + $i);
            $months[$i] = $month;
            $labels[$i] = $month->format('M Y');
        }

        $rows = FinancialLedger::forFiscalYear($fiscalYear)
            ->selectRaw('type, MONTH(ledger_date) as m, YEAR(ledger_date) as y, SUM(amount) as total')
            ->groupByRaw('type, MONTH(ledger_date), YEAR(ledger_date)')
            ->get();

        foreach ($rows as $row) {
            foreach ($months as $idx => $month) {
                if ($month->month === (int)$row->m && $month->year === (int)$row->y) {
                    if ($row->type === 'revenue') $revenue[$idx] = (float) $row->total;
                    else                          $expenses[$idx] = (float) $row->total;
                }
            }
        }

        return compact('labels', 'revenue', 'expenses');
    }

    // ── Expense Category Breakdown (for donut chart) ─────────────────────────

    public function getCategoryBreakdown(?string $from = null, ?string $to = null): Collection
    {
        return FinancialLedger::expense()
            ->dateRange($from, $to)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
    }

    // ── Project-wise Revenue & Expense Table ─────────────────────────────────

    public function getProjectBreakdown(?string $from = null, ?string $to = null): Collection
    {
        return FinancialLedger::with('project')
            ->dateRange($from, $to)
            ->whereNotNull('project_id')
            ->selectRaw('project_id, type, SUM(amount) as total, SUM(tax_amount) as tax')
            ->groupBy('project_id', 'type')
            ->get()
            ->groupBy('project_id');
    }

    // ── FBR Annex-A: Withholding Tax Register (Section 153) ─────────────────

    public function getFbrAnnexA(string $fiscalYear): Collection
    {
        // Only active expenses with tax > 0 qualify for Annex-A
        return \App\Models\Expense::withTrashed()
            ->with(['project', 'category', 'creator'])
            ->where('tax_amount', '>', 0)
            ->where(function ($query) use ($fiscalYear) {
                $query->whereHas('ledgerEntry', fn($q) => $q->where('fiscal_year', $fiscalYear))
                    ->orWhere(fn($q) => $q
                        ->whereYear('expense_date', '>=', $this->fyStartDate($fiscalYear)->year)
                        ->whereYear('expense_date', '<=', $this->fyEndDate($fiscalYear)->year)
                    );
            })
            ->orderBy('expense_date')
            ->get();
    }

    // ── Income & Expenditure Summary ─────────────────────────────────────────

    public function getIncomeSummary(string $fiscalYear): array
    {
        $kpis = $this->getKPIs(fiscalYear: $fiscalYear);

        $revenueByCategory = FinancialLedger::revenue()
            ->forFiscalYear($fiscalYear)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $expenseByCategory = FinancialLedger::expense()
            ->forFiscalYear($fiscalYear)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        return [
            'fiscal_year'          => $fiscalYear,
            'kpis'                 => $kpis,
            'revenue_by_category'  => $revenueByCategory,
            'expense_by_category'  => $expenseByCategory,
            'fy_start'             => $this->fyStartDate($fiscalYear),
            'fy_end'               => $this->fyEndDate($fiscalYear),
            'org'                  => $this->orgSettings(),
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function currentFiscalYear(): string
    {
        return FinancialLedger::fiscalYearFor(now(), FinancialSetting::fyStartMonth());
    }

    public function allFiscalYears(): Collection
    {
        return FinancialLedger::selectRaw('fiscal_year')
            ->groupBy('fiscal_year')
            ->orderByDesc('fiscal_year')
            ->pluck('fiscal_year');
    }

    public function fyStartDate(string $fy): Carbon
    {
        $year = (int) explode('-', $fy)[0];
        return Carbon::create($year, FinancialSetting::fyStartMonth(), 1);
    }

    public function fyEndDate(string $fy): Carbon
    {
        return $this->fyStartDate($fy)->addYear()->subDay();
    }

    public function orgSettings(): array
    {
        return [
            'name'    => FinancialSetting::get('org_name_for_tax', 'Prime Assessment & Testing Services'),
            'ntn'     => FinancialSetting::get('org_ntn', 'N/A'),
            'strn'    => FinancialSetting::get('org_strn', 'N/A'),
            'address' => FinancialSetting::get('org_address_for_tax', ''),
        ];
    }
}
