<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111; margin: 0; padding: 0; }
        .sheet { border: 1px solid #000; padding: 15px; height: 1000px; position: relative; }
        .header { display: flex; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px; }
        .logo-section { width: 70px; }
        .title-section { flex-grow: 1; text-align: center; }
        .title-section h1 { font-size: 14px; margin: 0; }
        .title-section h2 { font-size: 10px; margin: 5px 0; color: #444; }
        .photo-section { width: 90px; height: 110px; border: 1px solid #000; text-align: center; }
        
        .candidate-info { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .candidate-info td { padding: 3px 5px; border: 1px solid #ddd; }
        .lbl { font-weight: bold; background: #f5f5f5; width: 120px; }
        
        .barcode-area { border: 1px solid #000; padding: 10px; margin-top: 15px; text-align: center; background: #fdfdfd; }
        .bubble-area { margin-top: 20px; }
        .bubble-column { width: 19%; float: left; margin-right: 1%; }
        .bubble-row { margin-bottom: 8px; clear: both; }
        .q-num { font-weight: bold; display: inline-block; width: 15px; text-align: right; margin-right: 5px; }
        .bubble { display: inline-block; width: 14px; height: 14px; border: 1px solid #000; border-radius: 50%; text-align: center; line-height: 14px; font-size: 8px; margin-right: 2px; }
        
        .grid-header { background: #000; color: #fff; text-align: center; font-weight: bold; padding: 3px; margin-bottom: 10px; }
        .book-color-selection { margin-top: 15px; border: 1px solid #000; padding: 10px; }
        .color-box { display: inline-block; width: 60px; text-align: center; margin-right: 20px; }
        .color-bubble { width: 15px; height: 15px; border: 1px solid #000; border-radius: 50%; margin: 0 auto 3px; }
        
        .footer { position: absolute; bottom: 20px; width: 95%; border-top: 1px solid #000; padding-top: 5px; font-size: 8px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
@foreach($roster as $roll)
    @php
        $app = $roll->application;
        $candidate = $app->candidate;
        $user = $candidate->user;
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($roll->roll_no, $generator::TYPE_CODE_128, 2, 50));
    @endphp
    <div class="sheet">
        <div style="float: left; width: 80%;">
             <div style="font-size: 16px; font-weight: bold; color: #0a3d62;">PRIME ASSESSMENT & TESTING SERVICES</div>
             <div style="font-size: 11px; margin-top: 5px;">{{ $batch->project->org_name }}</div>
             <div style="font-size: 10px; color: #e84118; font-weight: bold; margin-top: 5px;">{{ $batch->project->name }}</div>
        </div>
        <div style="float: right; width: 100px; height: 120px; border: 1px solid #000; overflow: hidden;">
            @if($candidate->photo_path)
                <img src="{{ storage_path('app/public/' . $candidate->photo_path) }}" width="100" height="120" style="object-fit: cover;">
            @else
                <div style="text-align:center; padding-top: 50px; font-size: 8px; color: #999;">PHOTO</div>
            @endif
        </div>
        <div style="clear: both;"></div>

        <div class="barcode-area">
            <div><img src="data:image/png;base64,{{ $barcode }}" style="width: 200px; height: 50px;"></div>
            <div style="font-size: 12px; font-weight: bold; margin-top: 5px; letter-spacing: 2px;">{{ $roll->roll_no }}</div>
        </div>

        <table class="candidate-info">
            <tr>
                <td class="lbl">NAME</td>
                <td>{{ strtoupper($user->full_name) }}</td>
                <td class="lbl">FATHER NAME</td>
                <td>{{ strtoupper($candidate->father_name) }}</td>
            </tr>
            <tr>
                <td class="lbl">CNIC</td>
                <td>{{ $user->cnic }}</td>
                <td class="lbl">POST</td>
                <td>{{ strtoupper($roll->job->title) }}</td>
            </tr>
            <tr>
                <td class="lbl">CENTER CODE</td>
                <td>{{ $batch->center->tcid }}</td>
                <td class="lbl">BATCH</td>
                <td>{{ $batch->batch_number }}</td>
            </tr>
        </table>

        <div class="book-color-selection">
            <div style="font-weight: bold; margin-bottom: 5px;">QUESTION BOOK COLOR (Fill only one):</div>
            <div class="color-box"><div class="color-bubble"></div>YELLOW</div>
            <div class="color-box"><div class="color-bubble"></div>GREEN</div>
            <div class="color-box"><div class="color-bubble"></div>WHITE</div>
            <div class="color-box"><div class="color-bubble"></div>BLUE</div>
            <div class="color-box"><div class="color-bubble"></div>PINK</div>
        </div>

        <div class="bubble-area">
            <div class="grid-header">ANSWER BUBBLES (1 - 100)</div>
            @for($col = 0; $col < 5; $col++)
                <div class="bubble-column">
                    @for($q = 1; $q <= 20; $q++)
                        @php $qn = ($col * 20) + $q; @endphp
                        <div class="bubble-row">
                            <span class="q-num">{{ $qn }}.</span>
                            <span class="bubble">A</span>
                            <span class="bubble">B</span>
                            <span class="bubble">C</span>
                            <span class="bubble">D</span>
                            <span class="bubble">E</span>
                        </div>
                    @endfor
                </div>
            @endfor
            <div style="clear: both;"></div>
        </div>

        <div style="margin-top: 20px; border: 1px solid #000; padding: 10px;">
            <div style="font-weight: bold;">CANDIDATE SIGNATURE: __________________________</div>
            <div style="margin-top: 15px; font-weight: bold;">INVIGILATOR SIGNATURE: __________________________</div>
        </div>

        <div class="footer">
            Printed on: {{ now()->format('d M Y H:i') }} | Roll No: {{ $roll->roll_no }} | PATS Official Answer Sheet
        </div>
    </div>
    @if(!$loop->last) <div class="page-break"></div> @endif
@endforeach
</body>
</html>
