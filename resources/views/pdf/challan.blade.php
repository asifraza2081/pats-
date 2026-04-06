<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 10px; }
  body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 10px; line-height: 1.3; }
  .copy-wrapper { border: 1px solid #000; padding: 15px; margin-bottom: 10px; position: relative; height: 31%; }
  .header { display: table; width: 100%; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 10px; }
  .logo-area { display: table-cell; width: 20%; vertical-align: middle; }
  .title-area { display: table-cell; width: 60%; text-align: center; vertical-align: middle; }
  .copy-type { display: table-cell; width: 20%; text-align: right; font-weight: bold; font-size: 12px; vertical-align: middle; color: #d63031; }
  
  .org-name { font-size: 16px; font-weight: bold; margin: 0; color: #0a3d62; }
  .project-name { font-size: 10px; font-weight: normal; margin: 2px 0; color: #555; }
  
  .bill-branding { background: #f1f2f6; border: 1px solid #ccc; padding: 5px 10px; margin-bottom: 10px; display: table; width: 100%; }
  .bill-branding-left { display: table-cell; width: 50%; font-weight: bold; font-size: 11px; }
  .bill-branding-right { display: table-cell; width: 50%; text-align: right; }
  
  .identifier-box { border: 2px solid #000; padding: 10px; background: #fff; margin-bottom: 10px; }
  .consumer-id-label { font-size: 12px; font-weight: bold; color: #000; display: block; margin-bottom: 4px; }
  .consumer-id-value { font-size: 22px; font-weight: 800; color: #000; letter-spacing: 2px; }
  
  .details-table { width: 100%; border-collapse: collapse; }
  .details-table td { padding: 4px 0; border: none; vertical-align: top; }
  .label { font-weight: bold; width: 30%; }
  .value { width: 70%; border-bottom: 1px dotted #ccc; }
  
  .amount-area { display: table; width: 100%; margin-top: 10px; }
  .amount-box { display: table-cell; width: 60%; background: #0a3d62; color: #fff; padding: 8px; font-size: 14px; font-weight: bold; }
  .deadline-box { display: table-cell; width: 40%; border: 1px solid #0a3d62; padding: 8px; text-align: center; }
  
  .instructions { font-size: 8px; color: #444; margin-top: 8px; border-top: 1px solid #eee; padding-top: 5px; }
  .instruction-icons { margin-top: 4px; font-weight: bold; color: #000; }
  
  .cut-line { position: absolute; bottom: -8px; left: 0; right: 0; border-bottom: 1px dashed #666; font-size: 9px; text-align: center; }
  .sig-area { margin-top: 15px; display: table; width: 100%; }
  .sig { display: table-cell; width: 50%; text-align: center; padding-top: 20px; border-top: 1px solid #ccc; font-size: 9px; }
</style>
</head>
<body>
@php
  $project = $app->job->project;
  $payment = $app->payment;
  $candidate = $app->candidate;
  $user = $candidate->user;
  $copies = ['BANK COPY', 'PATS COPY', 'CANDIDATE COPY'];
@endphp

@foreach($copies as $copy)
<div class="copy-wrapper">
    <div class="header">
        <div class="logo-area"><strong style="font-size: 24px;">PATS</strong></div>
        <div class="title-area">
            <h1 class="org-name">PRIME ASSESSMENT & TESTING SERVICES</h1>
            <p class="project-name">{{ $project->name }}</p>
        </div>
        <div class="copy-type">{{ $copy }}</div>
    </div>

    <div class="bill-branding">
        <div class="bill-branding-left">1LINK / 1BILL Enabled</div>
        <div class="bill-branding-right">Pay via ATM / Mobile App / Any Bank</div>
    </div>

    <table class="details-table">
        <tr>
            <td class="label">Candidate:</td>
            <td class="value">{{ $user->full_name }} (CNIC: {{ $user->cnic }})</td>
        </tr>
        <tr>
            <td class="label">Job Post:</td>
            <td class="value">{{ $app->job->title }} ({{ $app->job->job_code }})</td>
        </tr>
    </table>

    <div class="identifier-box" style="margin-top: 10px;">
        <span class="consumer-id-label">1BILL CONSUMER ID / CHALLAN NO:</span>
        <span class="consumer-id-value">{{ $payment->challan_ref }}</span>
    </div>

    <div class="amount-area">
        <div class="amount-box">TOTAL AMOUNT: PKR {{ number_format($payment->amount) }}/-</div>
        <div class="deadline-box">
            <span style="font-size: 8px; display: block;">PAYMENT DEADLINE:</span>
            <strong>{{ $project->close_date?->format('d-M-Y') ?? 'N/A' }}</strong>
        </div>
    </div>

    <div class="instructions">
        <strong>How to pay:</strong> 1. Open any Banking App (HBL, Alfalah, EasyPaisa, JazzCash etc.) 2. Go to <strong>Bill Payments</strong> 3. Select <strong>1BILL</strong> 4. Select <strong>Invoice/Voucher</strong> 5. Enter Consumer ID and Pay.
        <div class="instruction-icons">M-Banking | ATM | OTC | Internet Banking</div>
    </div>

    <div class="sig-area">
        <div class="sig" style="border-top: none;"></div>
        <div class="sig">Candidate's Signature</div>
        <div class="sig" style="border-right: none;">Bank Officer Stamp & Signature</div>
    </div>

    @if($copy != 'CANDIDATE COPY')
    <div class="cut-line">- - - - - - - - - - - - - - - - - - - - - - - - - - - Cut Here - - - - - - - - - - - - - - - - - - - - - - - - - - -</div>
    @endif
</div>
@endforeach

</body>
</html>
