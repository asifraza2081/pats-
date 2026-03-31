<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 25px; }
  body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #000; line-height: 1.4; margin: 0; padding: 0; }
  .header-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
  .logo { width: 100px; }
  .header-text { text-align: center; }
  .header-text h1 { font-size: 20px; margin: 0; font-weight: bold; }
  .header-text p { font-size: 10px; margin: 2px 0; color: #333; }
  
  .title-section { text-align: center; margin-bottom: 20px; }
  .title-section h2 { font-size: 14px; text-decoration: underline; margin: 5px 0; }
  .title-section h3 { font-size: 16px; margin: 5px 0; }

  .content-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
  .content-table td { padding: 4px 0; vertical-align: top; }
  .label { font-weight: bold; width: 140px; text-align: right; padding-right: 15px !important; }
  .value { text-align: left; }
  
  .photo-box { width: 150px; height: 180px; border: 1px solid #000; text-align: center; float: right; margin-left: 20px; }
  .photo-box img { width: 100%; height: 100%; object-fit: cover; }
  
  .note-section { margin-top: 25px; border-top: 1px solid #999; padding-top: 10px; }
  .note-bold { font-weight: bold; margin-bottom: 5px; }
  .warning-list { margin: 10px 0; padding-left: 20px; }
  .warning-list li { margin-bottom: 5px; text-align: justify; font-size: 10px; }
  
  .urdu-text { direction: rtl; text-align: right; font-size: 11px; margin-top: 20px; font-weight: bold; border-top: 1px solid #ccc; padding-top: 15px; color: #d63031; }
  .footer { margin-top: 20px; text-align: center; font-size: 8px; border-top: 1px solid #000; padding-top: 5px; color: #666; }
  .watermark { position: fixed; top: 40%; left: 15%; width: 70%; opacity: 0.03; z-index: -1000; transform: rotate(-35deg); font-size: 120px; font-weight: bold; color: #0a3d62; }
  .barcode-container { margin-top: 10px; text-align: right; }
  .fingerprint { font-family: monospace; font-size: 7px; color: #999; margin-top: 5px; }
  .clearfix { clear: both; }
</style>
</head>
<body>
@php
  $project    = $app->job->project;
  $examRollno = $app->examRollno;
  $center     = $examRollno->center;
  $city       = $examRollno->city;
  $user       = $candidate->user;
  $batch      = $examRollno->batch;
  
  $photoPath = storage_path('app/public/' . $candidate->photo_path);
  $hasPhoto = $candidate->photo_path && file_exists($photoPath);

  // Secure Barcode Generation
  $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
  $barcode = base64_encode($generator->getBarcode($examRollno->roll_no, $generator::TYPE_CODE_128, 1.5, 40));
  
  // Document Fingerprint
  $fingerprint = hash('sha256', $examRollno->roll_no . $user->id . $batch->id . now()->toDateTimeString());
@endphp

<div class="watermark">PATS OFFICIAL</div>

<table class="header-table">
  <tr>
    <td class="logo" style="width: 100px;">
        <img src="{{ public_path('logo.png') }}" style="width: 85px;">
    </td>
    <td class="header-text">
        <h1 style="color: #0a3d62; font-size: 24px;">PRIME ASSESSMENT & TESTING SERVICES</h1>
        <p style="font-weight: bold; font-size: 12px; letter-spacing: 1px;">PATS - Merit & Transparency for a Better Future</p>
    </td>
  </tr>
</table>

<div class="title-section">
    <h2 style="background: #f8f9fa; padding: 5px; border-top: 1px solid #ddd; border-bottom: 2px solid #0a3d62;">{{ strtoupper($project->name) }}</h2>
    <div style="font-size: 18px; font-weight: bold; margin-top: 10px;">{{ strtoupper($app->job->title) }}</div>
    <h3>(Roll Number Slip)</h3>
</div>

<div class="main-content">
    <div class="photo-box">
        @if($hasPhoto)
            <img src="{{ $photoPath }}">
        @else
            <div style="padding-top: 80px; color: #999;">CANDIDATE<br>PHOTO</div>
        @endif
    </div>

    <table class="content-table" style="width: calc(100% - 180px);">
        <tr>
            <td class="label">Roll No :</td>
            <td class="value"><strong>{{ $examRollno->roll_no }}</strong></td>
        </tr>
        <tr>
            <td class="label">Name :</td>
            <td class="value">{{ strtoupper($user->full_name) }}</td>
        </tr>
        <tr>
            <td class="label">Father Name :</td>
            <td class="value">{{ strtoupper($candidate->father_name) }}</td>
        </tr>
        <tr>
            <td class="label">CNIC :</td>
            <td class="value">{{ $user->cnic }}</td>
        </tr>
        <tr>
            <td class="label">Paper Type :</td>
            <td class="value">{{ strtoupper($app->job->title) }}</td>
        </tr>
        <tr>
            <td class="label">Test Date :</td>
            <td class="value">{{ $batch->test_date ? $batch->test_date->format('l d M, Y') : 'TBD' }}</td>
        </tr>
        <tr>
            <td class="label">Reporting Time :</td>
            <td class="value">{{ $batch->reporting_time ? \Carbon\Carbon::parse($batch->reporting_time)->format('h:i A') : 'TBD' }}</td>
        </tr>
        <tr>
            <td class="label">Test Center :</td>
            <td class="value"><strong>{{ strtoupper($center->name ?? 'TBD') }}</strong><br><span style="font-size: 9px;">{{ strtoupper($center->address ?? '') }}</span></td>
        </tr>
    </table>
    
    <div class="barcode-container">
        <img src="data:image/png;base64,{{ $barcode }}" style="width: 250px; height: 50px;">
        <div style="font-family: monospace; font-size: 11px; font-weight: bold; margin-top: 3px;">* {{ $examRollno->roll_no }} *</div>
    </div>
</div>

<div class="clearfix"></div>

<div class="note-section">
    <div class="note-bold">NOTE: Candidates are provisionally allowed to appear in the test. Subject to verification of credentials and eligibility criteria.</div>
    <div class="note-bold">Note (Warning):</div>
    <ul class="warning-list">
        <li>Candidates are required to bring your Roll Number Slip along with one of the following:</li>
        <li>Original CNIC or Passport (for candidates aged 18 years or above), or B-Form and Matriculation Certificate containing a photograph (for candidates under 18 years of age).</li>
        <li>Failure to produce the Roll Number Slip and original identification will result in denial of entry to the examination hall.</li>
        <li>Candidates must also bring a clipboard and a ballpoint pen (black or blue).</li>
        <li>Mobile Phone, Calculators or Any Other Electronic Device is Not Allowed in The Test Center Premises.</li>
        <li>No facility for mobile collection will be provided.</li>
        <li>Candidates found carrying such devices will have them confiscated and their test paper cancelled.</li>
        <li>If your CNIC or B-Form has been lost, you must bring the original NADRA token along with any other original document containing your photograph.</li>
        <li>Candidates must sit only on the seats allotted to them and display their Roll Number Slip when asked by the invigilator.</li>
        <li>Talking, borrowing items or any kind of unfair means during the test will result in immediate disqualification.</li>
        <li>No candidate will be allowed to enter the examination hall after the test has started.</li>
    </ul>
</div>

<div class="urdu-text">
    احتیاط: ٹیٹ سنٹر کی حدود میں موبائل فون لانا سختی سے منع ہے موبائل اور الیکٹرانک آلات کے لیے آپ کی جامع تلاشی لی جاسکتی ہے اور برآمد ہونے کی صورت میں ضبط کرکے پیپر Cancel کردیا جائے گا۔
</div>

<div class="footer">
    Verification ID: <span style="font-family: monospace;">{{ substr($fingerprint, 0, 16) }}</span> | 
    Printed on: {{ now()->timezone('Asia/Karachi')->format('d-M-Y h:i A') }}<br>
    &copy; {{ date('Y') }} PATS (Prime Assessment & Testing Services) | Security Hash: {{ substr($fingerprint, 16, 32) }}
    <div class="fingerprint">DOC-ID: {{ $fingerprint }}</div>
</div>

</body>
</html>
