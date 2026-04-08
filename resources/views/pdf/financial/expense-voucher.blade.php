<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 20px 25px; size: A5 portrait; }
  body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #000; margin: 0; }
  .header { border-bottom: 2px solid #1B4F72; padding-bottom: 8px; margin-bottom: 12px; text-align: center; }
  .org { font-size: 13px; font-weight: bold; color: #1B4F72; }
  .doc-title { font-size: 11px; font-weight: bold; margin-top: 2px; }
  .meta { font-size: 8px; color: #888; }
  
  .voucher-no { float: right; background: #1B4F72; color: white; padding: 4px 10px; font-weight: bold; border-radius: 3px; font-size: 11px; }
  .section-label { font-weight: bold; background: #f5f5f5; padding: 4px 8px; margin: 10px 0 5px 0; font-size: 9px; text-transform: uppercase; border-left: 3px solid #1B4F72; }
  
  table.detail { width: 100%; border-collapse: collapse; }
  table.detail td { padding: 5px 8px; border-bottom: 1px solid #eee; font-size: 9px; }
  table.detail .label { font-weight: bold; color: #555; width: 120px; }
  
  .amount-box { border: 2px solid #1B4F72; border-radius: 4px; margin: 12px 0; text-align: center; padding: 10px; }
  .amount-row { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #eee; font-size: 10px; }
  .amount-total { font-weight: bold; font-size: 13px; color: #1B4F72; margin-top: 6px; }
  
  .footer { margin-top: 15px; border-top: 1px solid #ccc; padding-top: 8px; font-size: 8px; color: #888; text-align: center; }
  .sig-row { width:100%; margin-top: 20px; }
  .sig-row td { text-align:center; font-size:9px; padding-top: 20px; border-top: 1px solid #000; }
</style>
</head>
<body>

<div class="header">
  <div class="org">{{ strtoupper($org['name']) }}</div>
  <div class="doc-title">PAYMENT VOUCHER</div>
  @if($org['address']) <div class="meta">{{ $org['address'] }}</div> @endif
</div>

<div>
  <div class="voucher-no">Voucher# {{ $expense->voucher_no ?: 'EXP-' . str_pad($expense->id, 5, '0', STR_PAD_LEFT) }}</div>
  <div style="font-size: 9px; color: #888;">Date: <strong>{{ $expense->expense_date->format('d-M-Y') }}</strong></div>
</div>

<div style="clear:both; margin-top: 10px;"></div>

<div class="section-label">Expense Details</div>
<table class="detail">
  <tr><td class="label">Project</td><td>{{ $expense->project?->name ?? 'General / Administrative' }}</td></tr>
  <tr><td class="label">Category</td><td>{{ $expense->category?->name ?? '—' }}</td></tr>
  <tr><td class="label">Description</td><td>{{ $expense->description }}</td></tr>
  @if($expense->notes)<tr><td class="label">Notes</td><td>{{ $expense->notes }}</td></tr>@endif
  <tr><td class="label">Entered By</td><td>{{ $expense->creator?->name ?? '—' }} on {{ $expense->created_at?->format('d-M-Y') }}</td></tr>
</table>

<div class="section-label">Payee Information</div>
<table class="detail">
  <tr><td class="label">Recipient Name</td><td>{{ $expense->recipient_name ?? '—' }}</td></tr>
  @if($expense->recipient_ntn) <tr><td class="label">NTN</td><td>{{ $expense->recipient_ntn }}</td></tr> @endif
  @if($expense->recipient_cnic) <tr><td class="label">CNIC</td><td>{{ $expense->recipient_cnic }}</td></tr> @endif
</table>

<div class="amount-box">
  <table style="width:100%; font-size:10px;">
    <tr>
      <td>Gross Amount</td>
      <td style="text-align:right; font-weight:bold;">PKR {{ number_format($expense->gross_amount, 2) }}</td>
    </tr>
    <tr>
      <td>WHT @ {{ $expense->tax_rate }}% <span style="font-size:8px; color:#888;">(Deducted at source)</span></td>
      <td style="text-align:right; color: #e74c3c;">— PKR {{ number_format($expense->tax_amount, 2) }}</td>
    </tr>
    <tr>
      <td colspan="2"><hr style="margin:5px 0; border:1px solid #1B4F72;"></td>
    </tr>
    <tr>
      <td class="amount-total">NET AMOUNT PAYABLE</td>
      <td class="amount-total" style="text-align:right;">PKR {{ number_format($expense->net_amount, 2) }}</td>
    </tr>
  </table>
</div>

<table class="sig-row">
  <tr>
    <td>Prepared By</td>
    <td>Approved By</td>
    <td>Received By (Payee)</td>
  </tr>
</table>

<div class="footer">
  This voucher is an official financial record of {{ $org['name'] }}.
  @if($org['ntn']) | NTN: {{ $org['ntn'] }} @endif
  | Generated: {{ now()->format('d-M-Y H:i') }}
</div>

</body>
</html>
