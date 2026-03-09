<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; margin: 0; padding: 20px; }
  .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #0a3d62; padding-bottom: 10px; }
  .header h1 { font-size: 20px; color: #0a3d62; margin: 0; font-weight: bold; letter-spacing: 1px; }
  .header h3 { font-size: 12px; color: #555; margin: 3px 0 0; font-weight: normal; }
  .challan-title { text-align: center; font-size: 14px; font-weight: bold; color: #e84118; margin: 10px 0; text-transform: uppercase; border: 1px dashed #e84118; padding: 4px; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  table td { border: 1px solid #ccc; padding: 6px 10px; }
  .label { font-weight: bold; width: 35%; background: #f9f9f9; }
  .amount-box { border: 2px solid #0a3d62; text-align: center; padding: 12px; margin-top: 15px; font-size: 16px; font-weight: bold; color: #0a3d62; }
  .bank-section { background: #eef4fb; border: 1px solid #0a3d62; padding: 10px; margin-top: 15px; }
  .bank-section h4 { margin: 0 0 6px; color: #0a3d62; font-size: 12px; }
  .footer { margin-top: 20px; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 8px; text-align: center; }
  .cut-line { border: none; border-top: 2px dashed #999; margin: 20px 0; }
</style>
</head>
<body>
@php
  $project = $app->job->project;
  $payment = $app->payment;
  $candidate = $app->candidate;
  $user = $candidate->user;
@endphp

{{-- BANK COPY --}}
<div class="header">
  <h1>PRIME ASSESSMENT &amp; TESTING SERVICES</h1>
  <h3>{{ $project->org_name }}</h3>
</div>
<div class="challan-title">Fee Deposit Challan — Bank Copy</div>

<table>
  <tr><td class="label">Challan Reference No.</td><td><strong>{{ $payment->challan_ref }}</strong></td></tr>
  <tr><td class="label">Candidate Name</td><td>{{ $user->full_name }}</td></tr>
  <tr><td class="label">Father's Name</td><td>{{ $candidate->father_name }}</td></tr>
  <tr><td class="label">CNIC</td><td>{{ $user->cnic }}</td></tr>
  <tr><td class="label">Post Applied For</td><td>{{ $app->job->title }} (Code: {{ str_pad($app->job->job_code, 2, '0', STR_PAD_LEFT) }})</td></tr>
  <tr><td class="label">Project</td><td>{{ $project->name }}</td></tr>
  <tr><td class="label">Application Deadline</td><td>{{ $project->close_date?->format('d M, Y') ?? 'N/A' }}</td></tr>
</table>

<div class="amount-box">
  Fee Amount: PKR {{ number_format($payment->amount, 2) }}
</div>

<div class="bank-section">
  <h4>Payment Instructions</h4>
  Deposit this amount at any branch of the designated bank. Keep bank receipt for your records. Challan is valid until <strong>{{ $project->close_date?->format('d M, Y') ?? 'the application deadline' }}</strong>.
</div>

<div class="footer">Generated on {{ now()->format('d M Y, H:i') }} &nbsp;|&nbsp; PATS &nbsp;|&nbsp; This is a computer-generated document.</div>

<hr class="cut-line">

{{-- CANDIDATE COPY --}}
<div class="header">
  <h1>PRIME ASSESSMENT &amp; TESTING SERVICES</h1>
  <h3>{{ $project->org_name }}</h3>
</div>
<div class="challan-title">Fee Deposit Challan — Candidate Copy</div>

<table>
  <tr><td class="label">Challan Reference No.</td><td><strong>{{ $payment->challan_ref }}</strong></td></tr>
  <tr><td class="label">Candidate Name</td><td>{{ $user->full_name }}</td></tr>
  <tr><td class="label">CNIC</td><td>{{ $user->cnic }}</td></tr>
  <tr><td class="label">Post Applied For</td><td>{{ $app->job->title }}</td></tr>
  <tr><td class="label">Project</td><td>{{ $project->name }}</td></tr>
  <tr><td class="label">Fee Amount</td><td><strong>PKR {{ number_format($payment->amount, 2) }}</strong></td></tr>
</table>

<div class="footer">Keep this copy for your records. Generated on {{ now()->format('d M Y, H:i') }}</div>
</body>
</html>
