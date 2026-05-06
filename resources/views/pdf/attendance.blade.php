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
        
        .names-row { border-bottom: 1px solid #000; margin-bottom: 15px; padding-bottom: 5px; }
        .names-row span { margin-right: 50px; font-weight: bold; }

        .attendance-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .attendance-table th, .attendance-table td { border: 1px solid #000; padding: 7px 5px; text-align: left; }
        .attendance-table th { background: #eee; text-align: center; font-weight: bold; font-size: 11px; }
        .attendance-table td { font-size: 11px; }
        .center-text { text-align: center; }
        
        .signature-box { width: 180px; height: 30px; }

        .footer-stats { float: right; width: 300px; margin-top: 5px; }
        .total-box { border: 1px solid #000; height: 30px; line-height: 30px; font-weight: bold; display: flex; align-items: center; }
        .total-label { background: #eee; width: 150px; text-align: center; border-right: 1px solid #000; float: left; }
        .total-value { width: 148px; float: left; text-align: center; }

        .supervisor-section { margin-top: 30px; clear: both; }
        .sig-row { width: 100%; margin-top: 40px; }
        .sig-col { width: 32%; display: inline-block; text-align: center; border-top: 1px solid #000; padding-top: 5px; font-weight: bold; font-size: 10px; vertical-align: top; }
        
        .instruction-note { font-size: 9px; font-weight: bold; margin-top: 5px; }
        .batch-barcode { float: right; margin-top: -10px; }
        .clearfix { clear: both; }

        /* Page break wrapper */
        .page-wrapper {
            position: relative;
        }
        .page-break {
            page-break-after: always;
        }
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
        
        $sr = 1;
    @endphp

    <div class="page-info"></div>

    @foreach($roster as $jobId => $rolls)
        @php 
            $jobTitle = $rolls->first()->job->title;
            // Hard paginator to 30 candidates max per sheet 
            $chunks = $rolls->chunk(30);
        @endphp

        @foreach($chunks as $chunkIndex => $chunk)
        <div class="page-wrapper {{ (!$loop->parent->last || !$loop->last) ? 'page-break' : '' }}">
            <table class="header-table" style="border-bottom: none; margin-bottom: 5px;">
                <tr>
                    <td class="logo" style="width: 100px;">
                        <img src="{{ public_path('logo.png') }}" style="width: 80px;">
                    </td>
                    <td class="company-name">
                        <h1 style="color: #000; font-size: 28px; font-family: 'Arial Black', sans-serif;">NATIONAL TESTING SERVICE PAKISTAN</h1>
                        <div style="font-weight: bold; font-size: 16px; border-top: 1px solid #000; display: inline-block; padding: 2px 20px;">Attendance Sheet Candidates</div>
                    </td>
                    <td width="120" style="text-align: right; font-size: 9px;">
                        Page {{ $chunkIndex + 1 }} of {{ $chunks->count() }}<br>
                        <strong>{{ strtoupper($batch->center->tcid) }}</strong>
                    </td>
                </tr>
            </table>

            <table class="meta-table" style="margin-bottom: 10px;">
                <tr>
                    <td width="33%">
                        <strong>{{ strtoupper($city->name) }}</strong><br>
                        {{ strtoupper($project->name) }}
                    </td>
                    <td width="33%" style="text-align: center;">
                        <strong>{{ strtoupper($jobTitle) }}</strong><br>
                        BATCH-{{ $batch->batch_number }} ({{ \Carbon\Carbon::parse($batch->reporting_time)->format('h:i A') }})
                    </td>
                    <td width="33%" style="text-align: right;">
                        <strong>{{ $batch->test_date->format('l dS M, Y') }}</strong><br>
                        <img src="data:image/png;base64,{{ $barcode }}" style="height: 25px;">
                    </td>
                </tr>
            </table>

            <div class="names-row" style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px 0;">
                <span>Invigilator Name: __________________________</span>
                <span>Supervisor Name: __________________________</span>
            </div>

            <table class="attendance-table">
                <thead>
                    <tr>
                        <th width="40">Sr.#</th>
                        <th width="100">Roll #</th>
                        <th style="text-align: center;">Candidate Name</th>
                        <th width="180">Candidate Signature</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chunk as $roll)
                    <tr>
                        <td class="center-text">{{ $sr++ }}</td>
                        <td class="center-text"><strong>{{ $roll->roll_no }}</strong></td>
                        <td>
                            <div style="font-weight: bold; font-size: 12px;">{{ strtoupper($roll->application->candidate->user->full_name) }}</div>
                            <div style="font-size: 9px;">{{ $roll->application->candidate->father_name }}</div>
                        </td>
                        <td class="signature-box"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="footer-stats" style="margin-top: 0;">
                <div class="total-box">
                    <div class="total-label">TOTAL PRESENT</div>
                    <div class="total-value"></div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="supervisor-section" style="margin-top: 10px;">
                <div class="instruction-note">
                    <strong>ONLY SUPERVISOR:</strong> 1. Mark Absent with Red Pen Only
                </div>

                <div class="sig-row" style="margin-top: 30px;">
                    <div class="sig-col" style="width: 25%; border-top: 1px solid #000;">Invigilator Signature</div>
                    <div class="sig-col" style="width: 25%; margin: 0 10%; border-top: 1px solid #000;">Supervisor Signature</div>
                    <div class="sig-col" style="width: 25%; border-top: 1px solid #000;">Chief Supervisor Signature</div>
                </div>
            </div>
        </div>
        @endforeach
    @endforeach

</body>
</html>
