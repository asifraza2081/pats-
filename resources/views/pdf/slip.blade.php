<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; margin: 0; padding: 15px; }
  .slip-outer { border: 2px solid #0a3d62; padding: 10px; }
  .header { display: flex; align-items: flex-start; justify-content: space-between; border-bottom: 2px solid #0a3d62; padding-bottom: 8px; margin-bottom: 8px; }
  .header-text h1 { font-size: 15px; color: #0a3d62; font-weight: bold; margin: 0; }
  .header-text h2 { font-size: 11px; color: #444; font-weight: normal; margin: 2px 0; }
  .header-text h3 { font-size: 10px; color: #e84118; font-weight: bold; margin: 4px 0 0; text-transform: uppercase; }
  .photo-box { width: 80px; height: 95px; border: 1px solid #999; text-align: center; font-size: 8px; color: #999; padding-top: 35px; flex-shrink: 0; }
  .info-table { width: 100%; border-collapse: collapse; margin: 8px 0; }
  .info-table td { padding: 4px 6px; border-bottom: 1px solid #eee; font-size: 10px; }
  .info-table .lbl { font-weight: bold; width: 30%; color: #0a3d62; white-space: nowrap; }
  .info-table .val { color: #111; }
  .roll-highlight { text-align: center; background: #0a3d62; color: #fff; font-size: 18px; font-weight: bold; padding: 6px; letter-spacing: 3px; margin: 8px 0 4px; }
  .barcode { text-align: center; margin: 4px 0 10px; }
  .instructions { border-top: 1px solid #0a3d62; margin-top: 8px; padding-top: 6px; }
  .instructions h4 { color: #0a3d62; font-size: 10px; margin: 0 0 4px; }
  .instructions ol { margin: 0; padding-left: 14px; font-size: 9px; color: #333; }
  .instructions ol li { margin-bottom: 2px; }
  .footer-note { text-align: center; font-size: 8px; color: #888; margin-top: 8px; border-top: 1px dashed #ccc; padding-top: 4px; }
  .caution { text-align: center; font-size: 8.5px; color: #e84118; margin-top: 4px; font-style: italic; }
</style>
</head>
<body>
@php
  use Picqer\Barcode\BarcodeGeneratorSVG;
  $project    = $app->job->project;
  $examRollno = $app->examRollno;
  $center     = $examRollno->testCenter;
  $city       = $examRollno->city;
  $user       = $candidate->user;

  $generator = new BarcodeGeneratorSVG();
  $barcodeSvg = $generator->getBarcode($examRollno->roll_no, $generator::TYPE_CODE_128, 2.5, 50);
@endphp

<div class="slip-outer">
  {{-- Header --}}
  <div class="header">
    <div class="header-text">
      <h1>PRIME ASSESSMENT &amp; TESTING SERVICES</h1>
      <h2>{{ $project->org_name }}</h2>
      <h3>Roll Number Slip &mdash; {{ $project->name }}</h3>
    </div>
    <div class="photo-box">
      @if($candidate->photo_path)
        <img src="{{ storage_path('app/public/' . $candidate->photo_path) }}" width="78" height="93" style="object-fit:cover;">
      @else
        Photo
      @endif
    </div>
  </div>

  {{-- Roll Number Highlight --}}
  <div class="roll-highlight">{{ $examRollno->roll_no }}</div>

  {{-- Barcode --}}
  <div class="barcode">{!! $barcodeSvg !!}</div>

  {{-- Candidate Info Table --}}
  <table class="info-table">
    <tr><td class="lbl">Candidate Name</td><td class="val">{{ $user->full_name }}</td><td class="lbl">Father's Name</td><td class="val">{{ $candidate->father_name }}</td></tr>
    <tr><td class="lbl">CNIC / NIC No.</td><td class="val">{{ $user->cnic }}</td><td class="lbl">Gender</td><td class="val">{{ $candidate->gender }}</td></tr>
    <tr><td class="lbl">Post Applied For</td><td class="val" colspan="3">{{ $app->job->title }} {{ $app->job->department ? '('.$app->job->department.')' : '' }}</td></tr>
    <tr><td class="lbl">Test Date</td><td class="val">{{ $examRollno->test_date ? \Carbon\Carbon::parse($examRollno->test_date)->format('l, d M Y') : 'TBD' }}</td><td class="lbl">Reporting Time</td><td class="val">{{ $examRollno->reporting_time ? \Carbon\Carbon::parse($examRollno->reporting_time)->format('h:i A') : 'TBD' }}</td></tr>
    <tr><td class="lbl">Test Start Time</td><td class="val">{{ $examRollno->start_time ? \Carbon\Carbon::parse($examRollno->start_time)->format('h:i A') : 'TBD' }}</td><td class="lbl">Batch No.</td><td class="val">BATCH-{{ $examRollno->batch_no ?? '01' }}</td></tr>
    <tr><td class="lbl">Test Center</td><td class="val" colspan="3">{{ $center->name ?? 'TBD' }}</td></tr>
    <tr><td class="lbl">Center Address</td><td class="val" colspan="3">{{ $center->address ?? 'TBD' }}</td></tr>
    <tr><td class="lbl">City / TCID</td><td class="val">{{ $city->name ?? 'TBD' }}</td><td class="lbl">TCID</td><td class="val">{{ $center->tcid ?? 'TBD' }}</td></tr>
  </table>

  {{-- Instructions --}}
  <div class="instructions">
    <h4>Instructions for Candidates:</h4>
    <ol>
      <li>Bring this slip (printed) along with your original CNIC on test day. No candidate will be allowed without this slip.</li>
      <li>Arrive at the test center at least <strong>30 minutes</strong> before the reporting time.</li>
      <li>No entry will be permitted after the test start time.</li>
      <li>Mobile phones, electronic devices, and calculators are strictly prohibited in the examination hall.</li>
      <li>Candidates found using unfair means will be disqualified and may face legal action.</li>
      <li>This slip is for information only. Appearing in the test does not guarantee selection.</li>
      <li>Candidates are provisionally allowed to appear subject to verification of credentials.</li>
    </ol>
  </div>

  <div class="caution">
    Candidates are provisionally allowed to appear in the test subject to verification of eligibility criteria &amp; original documents.
  </div>

  <div class="footer-note">
    Downloaded: {{ now()->format('d M Y H:i:s') }} &nbsp;|&nbsp; PATS &nbsp;|&nbsp; Roll No: {{ $examRollno->roll_no }}
  </div>
</div>
</body>
</html>
