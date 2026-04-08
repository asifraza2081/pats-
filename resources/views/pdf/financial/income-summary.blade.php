<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 25px; size: A4 portrait; }
  body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #000; margin: 0; }
  .header-table { width: 100%; border-bottom: 3px solid #0d6832; padding-bottom: 12px; margin-bottom: 20px; }
  .org-name { font-size: 18px; font-weight: bold; color: #0d6832; }
  .doc-title { font-size: 14px; font-weight: bold; }
  .meta { font-size: 9px; color: #555; margin-top: 2px; }
  
  .fy-bar { background: #0d6832; color: #fff; font-weight: bold; text-align: center; padding: 8px; font-size: 13px; margin-bottom: 20px; border-radius: 4px; }
  
  .section-title { font-size: 12px; font-weight: bold; background: #e8f5e9; padding: 6px 10px; margin: 15px 0 5px 0; border-left: 4px solid #0d6832; color: #0d6832; }
  .section-expense .section-title { background: #fdecea; border-left-color: #c0392b; color: #c0392b; }
  
  table.ie { width: 100%; border-collapse: collapse; }
  table.ie td, table.ie th { padding: 5px 8px; border: 1px solid #ddd; }
  table.ie thead th { background: #f5f5f5; font-weight: bold; }
  table.ie tfoot td { font-weight: bold; background: #e8f5e9; }
  .section-expense table.ie tfoot td { background: #fdecea; }
  .text-right { text-align: right; }
  
  .net-bar { text-align: center; padding: 15px; margin-top: 20px; border-radius: 6px; }
  .net-surplus { background: #0d6832; color: white; }
  .net-deficit  { background: #c0392b; color: white; }
  .net-amount { font-size: 22px; font-weight: bold; margin-top: 5px; }
  
  .footer { margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 8px; color: #888; }
  .sig-row { width: 100%; margin-top: 30px; }
  .sig-row td { width: 33%; padding-top: 30px; border-top: 1px solid #000; text-align: center; font-size: 9px; }
</style>
</head>
<body>

<table class="header-table">
  <tr>
    <td>
      <div class="org-name">{{ strtoupper($org['name']) }}</div>
      <div class="meta">NTN: {{ $org['ntn'] ?: 'N/A' }}</div>
      @if($org['address'])<div class="meta">{{ $org['address'] }}</div>@endif
    </td>
    <td style="text-align:right;">
      <div class="doc-title">INCOME & EXPENDITURE STATEMENT</div>
      <div class="meta">For FBR Annual Tax Return Filing</div>
      <div class="meta" style="margin-top:4px;">Generated: {{ now()->format('d-M-Y H:i') }}</div>
    </td>
  </tr>
</table>

<div class="fy-bar">
  FISCAL YEAR {{ $fiscal_year }} &nbsp;|&nbsp; {{ $fy_start->format('01 July Y') }} — {{ $fy_end->format('30 June Y') }}
</div>

{{-- INCOME --}}
<div class="section-income">
  <div class="section-title">A. INCOME / RECEIPTS</div>
  <table class="ie">
    <thead>
      <tr><th>Source of Income</th><th class="text-right">Amount (PKR)</th><th class="text-right">%</th></tr>
    </thead>
    <tbody>
      @foreach($revenue_by_category as $item)
      <tr>
        <td>{{ $item->category }}</td>
        <td class="text-right">{{ number_format($item->total, 2) }}</td>
        <td class="text-right">{{ $kpis['total_revenue'] > 0 ? number_format($item->total/$kpis['total_revenue']*100,1) : 0 }}%</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td>TOTAL INCOME (A)</td>
        <td class="text-right">{{ number_format($kpis['total_revenue'], 2) }}</td>
        <td class="text-right">100%</td>
      </tr>
    </tfoot>
  </table>
</div>

{{-- EXPENDITURE --}}
<div class="section-expense" style="margin-top:15px;">
  <div class="section-title">B. EXPENDITURE / PAYMENTS</div>
  <table class="ie">
    <thead>
      <tr><th>Nature of Expenditure</th><th class="text-right">Amount (PKR)</th><th class="text-right">%</th></tr>
    </thead>
    <tbody>
      @foreach($expense_by_category as $item)
      <tr>
        <td>{{ $item->category }}</td>
        <td class="text-right">{{ number_format($item->total, 2) }}</td>
        <td class="text-right">{{ $kpis['total_expense'] > 0 ? number_format($item->total/$kpis['total_expense']*100,1) : 0 }}%</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td>TOTAL EXPENDITURE (B)</td>
        <td class="text-right">{{ number_format($kpis['total_expense'], 2) }}</td>
        <td class="text-right">100%</td>
      </tr>
    </tfoot>
  </table>
</div>

{{-- Net Surplus / Deficit --}}
<div class="net-bar {{ $kpis['net_surplus'] >= 0 ? 'net-surplus' : 'net-deficit' }}">
  <div>NET {{ $kpis['net_surplus'] >= 0 ? 'SURPLUS' : 'DEFICIT' }} (A − B)</div>
  <div class="net-amount">PKR {{ number_format(abs($kpis['net_surplus']), 2) }}</div>
  <div style="font-size:9px; margin-top:3px; opacity:.8;">
    Income ({{ number_format($kpis['total_revenue'],2) }}) − Expenditure ({{ number_format($kpis['total_expense'],2) }})
  </div>
</div>

<div style="margin-top:15px; background:#fffde7; border:1px solid #f1c40f; padding:8px; font-size:9px; border-radius:3px;">
  <strong>Notes:</strong> (1) This statement is prepared on cash basis for the period {{ $fy_start->format('01 July Y') }} to {{ $fy_end->format('30 June Y') }}.
  (2) WHT of PKR {{ number_format($kpis['tax_deducted'],2) }} was deducted from vendor payments during this period (see Annex-A for details).
  (3) This statement should be attached with the annual Income Tax Return filed with FBR.
</div>

<table class="sig-row">
  <tr>
    <td>Prepared By</td>
    <td>Reviewed By</td>
    <td>Authorised Signatory</td>
  </tr>
</table>

<div class="footer" style="text-align:center;">
  {{ $org['name'] }} | NTN: {{ $org['ntn'] ?: 'N/A' }} | Generated via PATS Financial System | {{ now()->format('d-M-Y H:i:s') }}
</div>

</body>
</html>
