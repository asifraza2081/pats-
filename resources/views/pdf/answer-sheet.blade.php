<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 15px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 8px; color: #000; line-height: 1.1; margin: 0; padding: 0; }
        
        .top-meta { width: 100%; font-size: 9px; margin-bottom: 5px; border-bottom: 1px solid #000; padding-bottom: 3px; }
        .top-meta td { vertical-align: bottom; }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .logo-col { width: 80px; vertical-align: top; }
        .info-col { vertical-align: top; padding-left: 10px; }
        .sig-col { width: 220px; vertical-align: top; border-left: 1px solid #000; padding-left: 10px; }

        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 0; font-size: 11px; }
        .label { width: 90px; color: #333; }
        .val { font-weight: bold; border-bottom: 1px dotted #000; }

        .sig-table { width: 100%; border-collapse: collapse; }
        .sig-table td { padding: 4px 0; border-bottom: 1px solid #000; height: 18px; }
        .sig-label { font-size: 9px; width: 110px; font-weight: bold; }

        .mid-section { width: 100%; margin-bottom: 15px; height: 160px; }
        .color-box { width: 150px; border: 1px solid #000; padding: 5px; float: left; }
        .barcode-box { width: 420px; border: 1px solid #000; margin: 0 5px; float: left; text-align: center; }
        .photo-box { width: 110px; height: 140px; border: 1px solid #000; float: right; text-align: center; overflow: hidden; }
        .photo-box img { width: 100%; height: 100%; object-fit: contain; }

        .color-row { padding: 2px 0; font-size: 10px; }
        .bubble-circle { display: inline-block; width: 12px; height: 12px; border: 1px solid #000; border-radius: 50%; margin-right: 15px; vertical-align: middle; }

        .barcode-msg { font-size: 10px; font-weight: bold; border-top: 1px solid #000; margin-top: 5px; padding-top: 2px; }

        .instruction-bar { width: 100%; background: #eee; border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold; font-size: 10px; margin-bottom: 15px; clear: both; }

        .bubbles-container { width: 100%; clear: both; }
        .bubble-column { width: 19%; float: left; margin-right: 1%; }
        .bubble-row { margin-bottom: 3px; }
        .q-num { display: inline-block; width: 18px; font-weight: bold; text-align: right; margin-right: 5px; font-size: 10px; }
        .ans-bubble { display: inline-block; width: 16px; height: 16px; border: 1px solid #000; border-radius: 50%; text-align: center; line-height: 16px; font-size: 9px; margin-right: 2px; }

        .footer-sigs { margin-top: 30px; width: 100%; clear: both; }
        .footer-sig-box { width: 45%; border: 1px solid #000; float: left; padding: 10px; height: 40px; font-weight: bold; }
        .watermark { position: fixed; top: 45%; left: 15%; width: 70%; opacity: 0.02; z-index: -1000; transform: rotate(-35deg); font-size: 150px; font-weight: bold; color: #0a3d62; }
        .fingerprint { font-family: monospace; font-size: 7px; color: #999; text-align: center; margin-top: 10px; }
        .page-break { page-break-after: always; }
        .clearfix { clear: both; }
    </style>
</head>
<body>
@foreach($roster as $roll)
    @php
        $app = $roll->application;
        $candidate = $app->candidate;
        $user = $candidate->user;
        $project = $batch->project;
        
        // Barcode generation - Code 128
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($roll->roll_no, $generator::TYPE_CODE_128, 2, 50));
        
        // Document Fingerprint
        $fingerprint = hash('sha256', $roll->roll_no . $app->id . $batch->id);
    @endphp

    <div class="watermark">PATS OMR</div>

    <table class="top-meta">
        <tr>
            <td width="30%">{{ strtoupper($batch->center->city->name) }}-{{ $batch->center->tcid }}</td>
            <td width="40%" style="text-align: center;">{{ strtoupper($roll->job->title) }}</td>
            <td width="30%" style="text-align: right;">BATCH-{{ $batch->batch_number }} &nbsp; {{ strtoupper($project->name) }}</td>
        </tr>
    </table>

    <table class="header-table">
        <tr>
            <td class="logo-col">
                <img src="{{ public_path('logo.png') }}" style="width: 75px;">
            </td>
            <td class="info-col">
                <div style="font-size: 32px; font-weight: bold; color: #0a3d62; line-height: 1;">PATS</div>
                <div style="font-size: 10px; font-weight: bold; letter-spacing: 1px;">PRIME ASSESSMENT & TESTING SERVICES</div>
                <table class="info-table">
                    <tr><td class="label">Name :</td><td class="val">{{ strtoupper($user->full_name) }}</td></tr>
                    <tr><td class="label">Father Name :</td><td class="val">{{ strtoupper($candidate->father_name) }}</td></tr>
                    <tr><td class="label">CNIC :</td><td class="val">{{ $user->cnic }}</td></tr>
                    <tr><td class="label">Roll No :</td><td class="val" style="font-size: 16px;">{{ $roll->roll_no }}</td></tr>
                </table>
            </td>
            <td class="sig-col">
                <table class="sig-table">
                    <tr><td class="sig-label">PATS Signatory :</td><td></td></tr>
                    <tr><td class="sig-label">Candidate Signature :</td><td></td></tr>
                    <tr><td class="sig-label">Question Book Color :</td><td></td></tr>
                    <tr><td class="sig-label">Question Book No :</td><td></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="mid-section">
        <div class="color-box">
            <div style="font-weight: bold; border-bottom: 1px solid #000; margin-bottom: 5px;">Color of your Question Book<br><span style="font-size: 7px;">(Fill only one circle)</span></div>
            <div class="color-row"><span class="bubble-circle"></span> Yellow</div>
            <div class="color-row"><span class="bubble-circle"></span> Green</div>
            <div class="color-row"><span class="bubble-circle"></span> White</div>
            <div class="color-row"><span class="bubble-circle"></span> Blue</div>
            <div class="color-row"><span class="bubble-circle"></span> Pink</div>
        </div>

        <div class="barcode-box">
            <div style="padding-top: 15px;">
                <img src="data:image/png;base64,{{ $barcode }}" style="width: 350px; height: 60px;">
            </div>
            <div class="barcode-msg">Do not write or mark anything in this box</div>
            <div style="font-size: 12px; margin-top: 5px; font-weight: bold;">BATCH- {{ $project->code ?? 'PATS' }}/{{ $batch->test_date->format('d-m-Y') }}/</div>
        </div>

        <div class="photo-box">
            @if($candidate->photo_path && file_exists(storage_path('app/public/' . $candidate->photo_path)))
                <img src="{{ storage_path('app/public/' . $candidate->photo_path) }}">
            @else
                <div style="padding-top: 60px; color: #999;">PHOTO</div>
            @endif
        </div>
    </div>

    <div class="instruction-bar">
        Fill the appropriate circle completely LIKE THIS <span style="display:inline-block; width:12px; height:12px; background:#000; border-radius:50%; vertical-align:middle; margin:0 5px;"></span> &bull; Improper filled circles will be marked incorrect by the machine
    </div>

    <div class="bubbles-container">
        @for($col = 0; $col < 5; $col++)
            <div class="bubble-column">
                @for($q = 1; $q <= 25; $q++)
                    @php $qn = ($col * 25) + $q; @endphp
                    <div class="bubble-row">
                        <span class="q-num">{{ $qn }}.</span>
                        <span class="ans-bubble">A</span>
                        <span class="ans-bubble">B</span>
                        <span class="ans-bubble">C</span>
                        <span class="ans-bubble">D</span>
                        <span class="ans-bubble">E</span>
                    </div>
                @endfor
            </div>
        @endfor
    </div>

    <div class="clearfix"></div>

    <div class="clearfix"></div>
    <div class="fingerprint">OMR SECURE DOC: {{ substr($fingerprint, 0, 32) }} | &copy; {{ date('Y') }} PATS</div>

    @if(!$loop->last) <div class="page-break"></div> @endif
@endforeach
</body>
</html>
