<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 20px 25px; size: A4 landscape; }
  body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 0; }
  .header-table { width: 100%; border-bottom: 3px solid #1B4F72; padding-bottom: 10px; margin-bottom: 15px; }
  .org-name { font-size: 16px; font-weight: bold; color: #1B4F72; margin: 0; }
  .doc-title { font-size: 14px; font-weight: bold; text-align: center; margin: 0; }
  .meta { font-size: 9px; color: #666; }
  
  table.annex { width: 100%; border-collapse: collapse; margin-top: 10px; }
  table.annex thead th { background: #1B4F72 !important; color: #fff !important; padding: 6px 5px; font-size: 9px; border: 1px solid #144060; text-align: left; }
  table.annex tbody td { padding: 5px; font-size: 9px; border: 1px solid #ddd; vertical-align: top; }
  table.annex tbody tr:nth-child(even) { background: #f4f7fb; }
  table.annex tfoot td { background: #1B4F72 !important; color: #fff !important; padding: 6px 5px; font-size: 9px; border: 1px solid #144060; font-weight: bold; }
  .text-right { text-align: right; }
  .text-center { text-align: center; }
  .badge-missing { color: #c0392b; font-weight: bold; }
  
  .footer { margin-top: 20px; font-size: 8px; color: #888; border-top: 1px solid #ccc; padding-top: 10px; }
  .disclaimer { background: #fffde7; border: 1px solid #f1c40f; padding: 8px; margin-top: 15px; font-size: 8px; border-radius: 3px; }
</style>
</head>
<body>

<table class="header-table">
  <tr>
    <td>
      <div class="org-name">{{ strtoupper($org['name']) }}</div>
      <div class="meta">NTN: {{ $org['ntn'] ?: 'Not Provided' }} | STRN: {{ $org['strn'] ?: 'Not Provided' }}</div>
      @if($org['address'])<div class="meta">{{ $org['address'] }}</div>@endif
    </td>
    <td style="text-align:right;">
      <div class="doc-title">WITHHOLDING TAX REGISTER</div>
      <div style="font-size:11px; font-weight:bold; color:#1B4F72;">ANNEX-A (Section 153 — ITO 2001)</div>
      <div class="meta" style="margin-top:3px;">
        Fiscal Year: <strong>{{ $fy }}</strong> ({{ $fyDates['start'] }} — {{ $fyDates['end'] }})
      </div>
      <div class="meta">Generated: {{ now()->format('d-M-Y H:i') }}</div>
    </td>
  </tr>
</table>

<table class="annex">
  <thead>
    <tr>
      <th style="width:30px;">S.No</th>
      <th style="width:80px;">Date</th>
      <th style="width:130px;">Recipient Name</th>
      <th style="width:90px;">NTN / CNIC</th>
      <th>Description</th>
      <th style="width:100px;">Category</th>
      <th style="width:60px;">Voucher</th>
      <th class="text-right" style="width:90px;">Gross (PKR)</th>
      <th class="text-right" style="width:50px;">Rate</th>
      <th class="text-right" style="width:90px;">Tax (PKR)</th>
      <th class="text-right" style="width:90px;">Net (PKR)</th>
    </tr>
  </thead>
  <tbody>
    @forelse($expenses as $i => $expense)
    <tr>
      <td class="text-center">{{ $i + 1 }}</td>
      <td>{{ $expense->expense_date->format('d-M-Y') }}</td>
      <td>{{ $expense->recipient_name ?? '—' }}</td>
      <td>
        @if($expense->recipient_ntn)
          {{ $expense->recipient_ntn }}
        @elseif($expense->recipient_cnic)
          {{ $expense->recipient_cnic }}
        @else
          <span class="badge-missing">⚠ Missing</span>
        @endif
      </td>
      <td style="max-width:150px;">{{ Str::limit($expense->description, 60) }}</td>
      <td>{{ $expense->category?->name ?? '—' }}</td>
      <td>{{ $expense->voucher_no ?? '—' }}</td>
      <td class="text-right">{{ number_format($expense->gross_amount, 2) }}</td>
      <td class="text-right">{{ $expense->tax_rate }}%</td>
      <td class="text-right" style="font-weight:bold;">{{ number_format($expense->tax_amount, 2) }}</td>
      <td class="text-right">{{ number_format($expense->net_amount, 2) }}</td>
    </tr>
    @empty
    <tr><td colspan="11" class="text-center" style="padding:15px; color:#666;">No taxable expenses for this fiscal year.</td></tr>
    @endforelse
  </tbody>
  @if($expenses->isNotEmpty())
  <tfoot>
    <tr>
      <td colspan="7" class="text-right" style="font-weight:bold;">GRAND TOTAL:</td>
      <td class="text-right">{{ number_format($expenses->sum('gross_amount'), 2) }}</td>
      <td class="text-right">—</td>
      <td class="text-right">{{ number_format($totalTax, 2) }}</td>
      <td class="text-right">{{ number_format($expenses->sum('net_amount'), 2) }}</td>
    </tr>
  </tfoot>
  @endif
</table>

<div class="disclaimer">
  <strong>Certification:</strong> This Withholding Tax Register (Annex-A) is prepared under Section 153 of the Income Tax Ordinance, 2001 and represents all payments from which tax was deducted at source during Fiscal Year {{ $fy }}.
  The amounts shown are correct to the best of our knowledge and belief and shall be filed with the Federal Board of Revenue (FBR) as required.
</div>

<div class="footer">
  <table style="width:100%;">
    <tr>
      <td>Prepared by: ________________________________</td>
      <td class="text-center">Designation: ________________________________</td>
      <td class="text-right">Signature & Stamp: ________________________________</td>
    </tr>
    <tr>
      <td colspan="3" style="text-align:center; padding-top:8px; color:#aaa;">
        {{ $org['name'] }} | Generated via PATS Financial System on {{ now()->format('d-M-Y H:i:s') }}
      </td>
    </tr>
  </table>
</div>

</body>
</html>
