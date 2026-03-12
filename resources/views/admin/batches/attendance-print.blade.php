<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Sheet — Session #{{ $batch->id }}</title>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 10px; line-height: 1.3; color: #000; margin: 0; padding: 0; }
        @media print { .no-print { display: none; } @page { size: A4; margin: 1cm; } }
        
        .page-container { position: relative; min-height: 27cm; padding: 10px; border: 1px solid #eee; margin-bottom: 20px; page-break-after: always; }
        @media print { .page-container { border: none; margin-bottom: 0; } }

        .sheet-header { border: 2px solid #000; padding: 10px; margin-bottom: 10px; }
        .org-name { font-size: 16px; font-weight: 800; text-align: center; margin-bottom: 2px; text-transform: uppercase; }
        .doc-title { font-size: 12px; font-weight: 700; text-align: center; background: #000; color: #fff; padding: 4px; margin-bottom: 8px; }
        
        .meta-table { width: 100%; margin-bottom: 5px; }
        .meta-table td { padding: 2px 5px; border: none; font-size: 9px; vertical-align: top; }
        .label { font-weight: 700; text-transform: uppercase; color: #444; width: 80px; display: inline-block; }
        
        table.attendance-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.attendance-table th { border: 1px solid #000; padding: 6px 4px; background: #f1f5f9; font-size: 8px; text-transform: uppercase; }
        table.attendance-table td { border: 1px solid #000; padding: 6px 6px; font-size: 9px; height: 25px; }
        
        .col-sr { width: 30px; text-align: center; }
        .col-roll { width: 90px; font-weight: 800; text-align: center; }
        .col-name { width: 180px; }
        .col-cnic { width: 100px; text-align: center; }
        .col-sig { width: 140px; }
        
        .sheet-footer { margin-top: 15px; border: 1px solid #000; padding: 10px; }
        .stats-row { display: flex; justify-content: space-between; font-weight: 700; margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        
        .sig-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        .sig-item { text-align: center; }
        .sig-line { border-top: 1px solid #000; margin-top: 30px; padding-top: 4px; font-size: 8px; font-weight: 700; }
        
        .btn-print { background: #066fd1; color: #fff; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; margin: 20px; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center;">
        <button class="btn-print" onclick="window.print()">Print Official Attendance Sheets</button>
    </div>

    @php $pageNum = 1; $rowsPerPage = 22; @endphp

    @foreach($roster as $jobId => $candidates)
        @php 
            $job = $candidates->first()->job;
            $chunks = $candidates->chunk($rowsPerPage);
            $totalJobPages = $chunks->count();
        @endphp

        @foreach($chunks as $index => $chunk)
        <div class="page-container">
            <div class="sheet-header">
                <div class="org-name">Prime Assessment & Testing Services</div>
                <div class="doc-title">CANDIDATE ATTENDANCE SHEET</div>
                
                <table class="meta-table">
                    <tr>
                        <td><span class="label">Project:</span> {{ $batch->project->name }}</td>
                        <td><span class="label">Post:</span> {{ $job->title }}</td>
                        <td><span class="label">TCID:</span> {{ $batch->center->tcid }}</td>
                    </tr>
                    <tr>
                        <td><span class="label">Center:</span> {{ $batch->center->name }}</td>
                        <td><span class="label">City:</span> {{ $batch->center->city->name }}</td>
                        <td><span class="label">Session:</span> #{{ $batch->id }} (Shift {{ $batch->batch_number }})</td>
                    </tr>
                    <tr>
                        <td><span class="label">Test Date:</span> {{ $batch->test_date->format('d-M-Y') }}</td>
                        <td><span class="label">Start Time:</span> {{ date('h:i A', strtotime($batch->start_time)) }}</td>
                        <td><span class="label">Page:</span> {{ $index + 1 }} / {{ $totalJobPages }}</td>
                    </tr>
                </table>
            </div>

            <table class="attendance-table">
                <thead>
                    <tr>
                        <th class="col-sr">Sr.</th>
                        <th class="col-roll">Roll Number</th>
                        <th class="col-name">Candidate Name / Father Name</th>
                        <th class="col-cnic">CNIC / Identity</th>
                        <th class="col-sig">Signature / Thumb</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chunk as $i => $roll)
                    <tr>
                        <td class="col-sr text-center">{{ ($index * $rowsPerPage) + $i + 1 }}</td>
                        <td class="col-roll">{{ $roll->roll_no }}</td>
                        <td class="col-name">
                            <div class="fw-bold">{{ $roll->application->candidate->user->full_name }}</div>
                            <div style="font-size: 8px; color: #666;">F: {{ $roll->application->candidate->father_name }}</div>
                        </td>
                        <td class="col-cnic">{{ $roll->application->candidate->user->cnic }}</td>
                        <td class="col-sig"></td>
                    </tr>
                    @endforeach
                    
                    {{-- Fill remaining rows to keep footer at bottom --}}
                    @for($fill = $chunk->count(); $fill < $rowsPerPage; $fill++)
                    <tr>
                        <td class="col-sr">{{ ($index * $rowsPerPage) + $fill + 1 }}</td>
                        <td class="col-roll"></td>
                        <td class="col-name"></td>
                        <td class="col-cnic"></td>
                        <td class="col-sig"></td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            <div class="sheet-footer">
                <div class="stats-row">
                    <span>PRESENT: ____________</span>
                    <span>ABSENT: ____________</span>
                    <span>TOTAL ON SHEET: {{ $chunk->count() }}</span>
                </div>
                <div class="sig-row">
                    <div class="sig-item">
                        <div class="sig-line">Invigilator Name & Signature</div>
                    </div>
                    <div class="sig-item">
                        <div class="sig-line">Center Superintendent</div>
                    </div>
                    <div class="sig-item">
                        <div class="sig-line">PATS Representative</div>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 10px; font-size: 7px; color: #666; display: flex; justify-content: space-between;">
                <span>Security Code: {{ md5($batch->id . $job->id . $index) }}</span>
                <span>Generated: {{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        @endforeach
    @endforeach
</body>
</html>
