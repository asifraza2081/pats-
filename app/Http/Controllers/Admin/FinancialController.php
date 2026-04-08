<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialLedger;
use App\Models\FinancialSetting;
use App\Services\FinancialService;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function __construct(private FinancialService $service) {}

    // ── Dashboard ────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $fy      = $request->get('fy', $this->service->currentFiscalYear());
        $allFys  = $this->service->allFiscalYears();
        $kpis    = $this->service->getKPIs(fiscalYear: $fy);
        $trend   = $this->service->getMonthlyTrend($fy);
        $donut   = $this->service->getCategoryBreakdown(
            from: $this->service->fyStartDate($fy)->toDateString(),
            to:   $this->service->fyEndDate($fy)->toDateString()
        );
        $projects  = $this->service->getProjectBreakdown(
            from: $this->service->fyStartDate($fy)->toDateString(),
            to:   $this->service->fyEndDate($fy)->toDateString()
        );
        $recent    = FinancialLedger::with('project')->latest('ledger_date')->take(10)->get();
        $fbrMode   = FinancialSetting::fbrModeEnabled();

        return view('admin.financials.index', compact(
            'fy', 'allFys', 'kpis', 'trend', 'donut', 'projects', 'recent', 'fbrMode'
        ));
    }

    // ── Full Ledger ──────────────────────────────────────────────────────────

    public function ledger(Request $request)
    {
        $query = FinancialLedger::with('project', 'creator')->latest('ledger_date');

        if ($request->filled('type'))         $query->where('type', $request->type);
        if ($request->filled('project_id'))   $query->where('project_id', $request->project_id);
        if ($request->filled('fy'))           $query->forFiscalYear($request->fy);
        if ($request->filled('from'))         $query->where('ledger_date', '>=', $request->from);
        if ($request->filled('to'))           $query->where('ledger_date', '<=', $request->to);

        $entries  = $query->paginate(25)->withQueryString();
        $totals   = $query->selectRaw('SUM(CASE WHEN type="revenue" THEN amount ELSE 0 END) as rev, SUM(CASE WHEN type="expense" THEN amount ELSE 0 END) as exp')->first();
        $projects = \App\Models\Project::orderBy('name')->get(['id', 'name']);
        $allFys   = $this->service->allFiscalYears();

        return view('admin.financials.ledger', compact('entries', 'totals', 'projects', 'allFys'));
    }

    // ── Settings ─────────────────────────────────────────────────────────────

    public function settings()
    {
        $settings = \App\Models\FinancialSetting::all()->keyBy('key');
        return view('admin.financials.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $data = $request->validate([
            'default_gst_rate'    => 'required|numeric|min:0|max:50',
            'fbr_mode_enabled'    => 'nullable|boolean',
            'org_ntn'             => 'nullable|string|max:20',
            'org_strn'            => 'nullable|string|max:30',
            'org_name_for_tax'    => 'required|string|max:200',
            'org_address_for_tax' => 'nullable|string|max:500',
        ]);

        $data['fbr_mode_enabled'] = $request->boolean('fbr_mode_enabled') ? '1' : '0';

        \App\Models\FinancialSetting::setMany($data);

        return back()->with('success', 'Financial settings saved successfully.');
    }
}
