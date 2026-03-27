<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 25px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #000; line-height: 1.3; margin: 0; padding: 0; }
        
        .header-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 10px; }
        .logo { width: 80px; }
        .company-name { text-align: center; }
        .company-name h1 { font-size: 22px; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        
        .title-section { text-align: center; margin-bottom: 15px; }
        .title-section h2 { font-size: 16px; text-decoration: underline; margin: 2px 0; }
        
        /* Dynamic Page Numbering */
        .page-info { position: fixed; top: 0; right: 0; font-size: 9px; }
        .page-info:after { content: "Page " counter(page) " of " counter(pages); }

        .meta-table { width: 100%; margin-bottom: 15px; }
        .meta-table td { font-size: 11px; padding: 2px 0; }
        .meta-label { font-weight: bold; width: 100px; }
        
        .names-row { border-bottom: 1px solid #000; margin-bottom: 15px; padding-bottom: 5px; }
        .names-row span { margin-right: 50px; font-weight: bold; }

        .attendance-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .attendance-table th, .attendance-table td { border: 1px solid #000; padding: 8px 5px; text-align: left; }
        .attendance-table th { background: #eee; text-align: center; font-weight: bold; font-size: 11px; }
        .attendance-table td { font-size: 11px; }
        .center-text { text-align: center; }
        
        .signature-box { width: 180px; height: 35px; }

        .footer-stats { float: right; width: 300px; margin-top: 10px; }
        .total-box { border: 1px solid #000; height: 30px; line-height: 30px; font-weight: bold; display: flex; align-items: center; }
        .total-label { background: #eee; width: 150px; text-align: center; border-right: 1px solid #000; float: left; }
        .total-value { width: 148px; float: left; text-align: center; }

        .supervisor-section { margin-top: 40px; clear: both; }
        .sig-row { width: 100%; margin-top: 50px; }
        .sig-col { width: 32%; display: inline-block; text-align: center; border-top: 1px solid #000; padding-top: 5px; font-weight: bold; font-size: 10px; vertical-align: top; }
        
        .instruction-note { font-size: 9px; font-weight: bold; margin-top: 10px; }
        .watermark { position: fixed; top: 40%; left: 15%; width: 70%; opacity: 0.02; z-index: -1000; transform: rotate(-25deg); font-size: 100px; font-weight: bold; color: #0a3d62; }
        .batch-barcode { float: right; margin-top: -10px; }
        .clearfix { clear: both; }
    </style>
</head>
<body>
    @php
        $project = $batch->project;
        $center  = $batch->center;
        $city    = $center->city;

        // Batch Barcode for lookup
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $batchIdStr = "BATCH-" . $batch->id;
        $barcode = base64_encode($generator->getBarcode($batchIdStr, $generator::TYPE_CODE_128, 1.2, 35));
    @endphp

    <div class="watermark">PATS ATTENDANCE</div>

    {{-- Page Info --}}
    <div class="page-info"></div>

    <table class="header-table">
        <tr>
            <td class="logo">
                <img src="{{ public_path('logo.png') }}" style="width: 75px;">
            </td>
            <td class="company-name">
                <h1 style="color: #0a3d62;">PRIME ASSESSMENT & TESTING SERVICES</h1>
                <div style="font-weight: bold; font-size: 12px; letter-spacing: 2px;">Merit | Transparency | Excellence</div>
            </td>
            <td width="150" style="vertical-align: middle;">
                <div class="batch-barcode">
                    <img src="data:image/png;base64,{{ $barcode }}">
                    <div style="font-size: 8px; text-align: center; font-family: monospace;">* {{ $batchIdStr }} *</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="title-section">
        <h2>Attendance Sheet Candidates</h2>
    </div>

    <table class="meta-table">
        <tr>
            <td width="50%">
                <strong>{{ $center->tcid }}-{{ strtoupper($city->name) }}</strong><br>
                <strong>{{ strtoupper($project->name) }}</strong>
            </td>
            <td width="50%" style="text-align: right;">
                <strong>BATCH-{{ $batch->batch_number }} ({{ \Carbon\Carbon::parse($batch->reporting_time)->format('h:i A') }})</strong><br>
                <strong>{{ $batch->test_date->format('l dS M, Y') }}</strong>
            </td>
        </tr>
    </table>

    <div class="names-row">
        <span>Invigilator Name: __________________________</span>
        <span>Supervisor Name: __________________________</span>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th width="40">Sr.#</th>
                <th width="100">Roll #</th>
                <th>Candidate Name</th>
                <th width="200">Candidate Signature</th>
            </tr>
        </thead>
        <tbody>
            @php $sr = 1; @endphp
            @foreach($roster as $jobId => $rolls)
                @foreach($rolls as $roll)
                <tr>
                    <td class="center-text">{{ $sr++ }}</td>
                    <td class="center-text"><strong>{{ $roll->roll_no }}</strong></td>
                    <td>{{ strtoupper($roll->application->candidate->user->full_name) }}</td>
                    <td class="signature-box"></td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer-stats">
        <div class="total-box">
            <div class="total-label">TOTAL PRESENT</div>
            <div class="total-value"></div>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="supervisor-section">
        <div class="instruction-note">
            ONLY SUPERVISOR<br>
            1. Mark Absent with Red Pen Only
        </div>

        <div class="sig-row">
            <div class="sig-col">Invigilator Signature</div>
            <div class="sig-col" style="margin: 0 1%;">Supervisor Signature</div>
            <div class="sig-col">Chief Supervisor Signature</div>
        </div>
    </div>

</body>
</html>
