<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { font-size: 16px; margin: 0; color: #0a3d62; }
        .header h2 { font-size: 11px; margin: 5px 0; }
        
        .session-info { width: 100%; margin-bottom: 15px; }
        .session-info td { padding: 3px 0; }
        
        .attendance-table { width: 100%; border-collapse: collapse; }
        .attendance-table th, .attendance-table td { border: 1px solid #000; padding: 5px; text-align: left; }
        .attendance-table th { background: #f5f5f5; font-size: 8px; text-transform: uppercase; }
        
        .photo-cell { width: 50px; height: 60px; text-align: center; }
        .mark-cell { width: 40px; text-align: center; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 8px; text-align: right; }
        
        .sig-box { margin-top: 40px; }
        .sig-line { display: inline-block; width: 250px; border-top: 1px solid #000; text-align: center; padding-top: 5px; margin-right: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PRIME ASSESSMENT & TESTING SERVICES</h1>
        <h2>ATTENDANCE SHEET - {{ $batch->project->name }}</h2>
    </div>

    <table class="session-info">
        <tr>
            <td width="15%"><strong>CENTER:</strong></td>
            <td width="35%">{{ $batch->center->name }} ({{ $batch->center->tcid }})</td>
            <td width="15%"><strong>DATE:</strong></td>
            <td width="35%">{{ $batch->test_date->format('d M Y') }}</td>
        </tr>
        <tr>
            <td><strong>CITY:</strong></td>
            <td>{{ $batch->center->city->name }}</td>
            <td><strong>TIME:</strong></td>
            <td>{{ \Carbon\Carbon::parse($batch->start_time)->format('h:i A') }}</td>
        </tr>
    </table>

    <table class="attendance-table">
        <thead>
            <tr>
                <th width="30">SR#</th>
                <th width="80">ROLL NUMBER</th>
                <th>CANDIDATE DETAILS</th>
                <th>POST APPLIED</th>
                <th colspan="2" style="text-align:center">ATTENDANCE</th>
            </tr>
            <tr>
                <th colspan="4"></th>
                <th width="45" style="text-align:center">PRESENT</th>
                <th width="45" style="text-align:center">ABSENT</th>
            </tr>
        </thead>
        <tbody>
            @php $sr = 1; @endphp
            @foreach($roster as $jobId => $rolls)
                @foreach($rolls as $roll)
                <tr>
                    <td style="text-align:center">{{ $sr++ }}</td>
                    <td style="font-weight: bold; font-size: 10px;">{{ $roll->roll_no }}</td>
                    <td>
                        <strong>{{ strtoupper($roll->application->candidate->user->full_name) }}</strong><br>
                        CNIC: {{ $roll->application->candidate->user->cnic }}<br>
                        F/N: {{ $roll->application->candidate->father_name }}
                    </td>
                    <td style="font-size: 8px;">{{ $roll->job->title }}</td>
                    <td style="border: 1px solid #000;"></td>
                    <td style="border: 1px solid #000;"></td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="sig-box">
        <div class="sig-line"><strong>EXAMINER SIGNATURE</strong><br>(Official Stamp)</div>
        <div class="sig-line"><strong>SUPERVISOR SIGNATURE</strong><br>(Center Head)</div>
    </div>

    <div class="footer">
        Generated on: {{ now()->format('d M Y H:i') }} | PATS Verification System
    </div>
</body>
</html>
