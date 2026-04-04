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
  .title-section h3 { font-size: 18px; margin: 5px 0; color: #0a3d62; }

  .content-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  .content-table td { padding: 6px 0; vertical-align: top; border-bottom: 1px solid #eee; }
  .label { font-weight: bold; width: 150px; text-align: right; padding-right: 15px !important; color: #555; }
  .value { text-align: left; font-weight: 500; }
  
  .result-box { background: #f0f7ff; border: 2px solid #0a3d62; padding: 20px; margin-top: 20px; text-align: center; border-radius: 10px; }
  .score-display { font-size: 32px; font-weight: 900; color: #0a3d62; margin-bottom: 5px; }
  .score-label { font-size: 12px; text-transform: uppercase; letter-spacing: 2px; color: #666; }
  
  .stats-table { width: 100%; margin-top: 15px; border-top: 1px solid #ccc; padding-top: 15px; }
  .stat-item { text-align: center; width: 33.33%; }
  .stat-val { font-size: 18px; font-weight: bold; color: #2d3436; }
  .stat-lbl { font-size: 9px; color: #636e72; text-transform: uppercase; }

  .photo-box { width: 120px; height: 140px; border: 1px solid #000; text-align: center; float: right; margin-left: 20px; }
  .photo-box img { width: 100%; height: 100%; object-fit: cover; }
  
  .footer { margin-top: 30px; text-align: center; font-size: 8px; border-top: 1px solid #000; padding-top: 10px; color: #666; }
  .watermark { position: fixed; top: 45%; left: 10%; width: 80%; opacity: 0.04; z-index: -1000; transform: rotate(-30deg); font-size: 100px; font-weight: bold; color: #0a3d62; }
  .qr-container { float: left; margin-top: 20px; }
  .stamp-container { float: right; margin-top: 20px; text-align: center; width: 150px; }
  .stamp { border: 2px solid #d63031; color: #d63031; padding: 5px; font-weight: bold; transform: rotate(-5deg); display: inline-block; border-radius: 5px; }
  .clearfix { clear: both; }
</style>
</head>
<body>
@php
  $result     = $app->result;
  $project    = $app->job->project;
  $candidate  = $app->candidate;
  $user       = $candidate->user;
  
  $photoPath = storage_path('app/public/' . $candidate->photo_path);
  $hasPhoto = $candidate->photo_path && file_exists($photoPath);

  // Document Fingerprint for verification
  $fingerprint = hash('sha256', $result->id . $app->id . $result->published_at);
@endphp

<div class="watermark">OFFICIAL RESULT</div>

<table class="header-table">
  <tr>
    <td class="logo" style="width: 100px;">
        <img src="{{ public_path('logo.png') }}" style="width: 85px;">
    </td>
    <td class="header-text">
        <h1 style="color: #0a3d62; font-size: 24px;">PRIME ASSESSMENT & TESTING SERVICES</h1>
        <p style="font-weight: bold; font-size: 12px; letter-spacing: 1px;">OFFICIAL PERFORMANCE REPORT & SCORECARD</p>
    </td>
  </tr>
</table>

<div class="title-section">
    <h2 style="background: #f8f9fa; padding: 5px; border-top: 1px solid #ddd; border-bottom: 2px solid #0a3d62;">{{ strtoupper($project->name) }}</h2>
    <div style="font-size: 16px; font-weight: bold; margin-top: 10px;">{{ strtoupper($app->job->title) }}</div>
    <h3>PROVISIONAL RESULT CARD</h3>
</div>

<div class="main-content">
    <div class="photo-box">
        @if($hasPhoto)
            <img src="{{ $photoPath }}">
        @else
            <div style="padding-top: 60px; color: #999;">CANDIDATE<br>PHOTO</div>
        @endif
    </div>

    <table class="content-table" style="width: calc(100% - 150px);">
        <tr>
            <td class="label">Roll Number :</td>
            <td class="value"><strong>{{ $result->roll_no }}</strong></td>
        </tr>
        <tr>
            <td class="label">Candidate Name :</td>
            <td class="value">{{ strtoupper($user->full_name) }}</td>
        </tr>
        <tr>
            <td class="label">Father Name :</td>
            <td class="value">{{ strtoupper($candidate->father_name) }}</td>
        </tr>
        <tr>
            <td class="label">CNIC Number :</td>
            <td class="value">{{ $user->cnic }}</td>
        </tr>
        <tr>
            <td class="label">Result Status :</td>
            <td class="value"><span style="color: {{ $result->result_status == 'pass' ? '#27ae60' : '#d63031' }}; font-weight: bold;">{{ strtoupper($result->result_status) }}</span></td>
        </tr>
    </table>

    <div class="result-box">
        <div class="score-label">Total Gained Score</div>
        <div class="score-display">{{ $result->score }} / {{ $result->total_marks }}</div>
        
        <table class="stats-table">
            <tr>
                <td class="stat-item">
                    <div class="stat-val">{{ $result->percentage }}%</div>
                    <div class="stat-lbl">Percentage</div>
                </td>
                <td class="stat-item" style="border-left: 1px solid #ccc; border-right: 1px solid #ccc;">
                    <div class="stat-val">{{ $result->percentile }}</div>
                    <div class="stat-lbl">Merit Percentile</div>
                </td>
                <td class="stat-item">
                    <div class="stat-val">{{ $result->published_at->format('d-M-Y') }}</div>
                    <div class="stat-lbl">Declaration Date</div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="clearfix"></div>

<div class="qr-and-stamp" style="margin-top: 30px;">
    <div class="qr-container">
        <!-- Result Verification QR Code (Mock) -->
        <div style="width: 80px; height: 80px; background: #eee; border: 1px solid #ccc; text-align: center; line-height: 80px; font-size: 8px;">VERIFICATION<br>QR CODE</div>
    </div>
    
    <div class="stamp-container">
        <div class="stamp">DIGITALLY VERIFIED</div>
        <div style="font-size: 9px; margin-top: 10px; font-weight: bold;">DIRECTOR OPERATIONS</div>
        <div style="font-size: 8px; color: #666;">Prime Assessment & Testing Services</div>
    </div>
</div>

<div class="clearfix"></div>

<div style="margin-top: 40px; padding: 15px; background: #fffcf0; border: 1px solid #f1c40f; border-radius: 5px;">
    <div style="font-weight: bold; font-size: 10px; color: #8a6d3b; margin-bottom: 5px;">DISCLAIMER & INSTRUCTIONS:</div>
    <ul style="font-size: 9px; color: #8a6d3b; margin: 0; padding-left: 15px;">
        <li>This result is provisional and subject to verification of original documents and eligibility criteria.</li>
        <li>Errors and omissions are excepted (E&OE).</li>
        <li>Percentile is calculated based on the performance of all candidates appearing for this specific job post.</li>
        <li>Any tampering or alteration of this result card will lead to disqualification and legal action.</li>
    </ul>
</div>

<div class="footer">
    Verification Fingerprint: <span style="font-family: monospace;">{{ $fingerprint }}</span><br>
    &copy; {{ date('Y') }} PATS | Secure Candidate Repository | Generated on {{ now()->format('d-M-Y h:i:s') }}
</div>

</body>
</html>
