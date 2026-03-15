<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Sheet — {{ $center->name ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', Arial, Helvetica, sans-serif; font-size: 11px; padding: 20px; color: #111; }
        .header { text-align: center; padding-bottom: 12px; border-bottom: 2px solid #000; margin-bottom: 14px; }
        .header h2 { font-size: 15px; font-weight: bold; letter-spacing: 0.5px; color: #0a3d62; }
        .header p  { font-size: 11px; font-weight: bold; margin-top: 3px; border: 1px solid #000; display: inline-block; padding: 2px 10px; }
        
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 8px; margin-bottom: 14px; padding: 12px; border: 1px solid #000; background: #fff; }
        .meta-grid div { margin-bottom: 4px; }
        .meta-grid strong { display: block; font-size: 8px; text-transform: uppercase; color: #555; letter-spacing: 0.3px; margin-bottom: 2px; }
        .meta-grid span { font-weight: bold; font-size: 10px; }
        
        .summary { margin: 10px 0; font-size: 11px; display: flex; justify-content: space-between; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f0f0f0; color: #000; padding: 6px 8px; font-size: 10px; text-align: left; border: 1px solid #000; }
        td { padding: 6px 8px; border: 1px solid #000; vertical-align: middle; }
        
        .sign-box { width: 50px; height: 25px; border: 1px solid #000; margin: 0 auto; }
        
        .footer { margin-top: 50px; display: flex; justify-content: space-between; }
        .footer .sign { text-align: center; width: 30%; }
        .footer .sign div { border-top: 1px solid #000; font-size: 9px; padding-top: 4px; font-weight: bold; }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
            .header h2 { color: #000; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:right; margin-bottom: 20px;">
    <button onclick="window.print()" style="padding: 8px 20px; background:#0a3d62; color:#fff; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">
        🖨️ Print Attendance Sheet
    </button>
    <button onclick="window.close()" style="margin-left:8px; padding: 8px 20px; background:#e5e7eb; color:#111; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">
        ✕ Close
    </button>
</div>

<div class="header">
    <h2>PRIME ASSESSMENT & TESTING SERVICES (PATS)</h2>
    <p>OFFICIAL ATTENDANCE SHEET</p>
</div>

<div class="meta-grid">
    <div style="grid-column: span 2;"><strong>Test Center</strong><span>{{ $center->name ?? 'N/A' }}</span></div>
    <div><strong>City</strong><span>{{ $center->city->name ?? '' }}</span></div>
    <div><strong>Center Code</strong><span>{{ $center->tcid ?? '—' }}</span></div>
    
    <div style="grid-column: span 2;"><strong>Project</strong><span>{{ $attendees[0]->project_name ?? '—' }}</span></div>
    <div><strong>Test Date</strong><span>{{ \Carbon\Carbon::parse($batch->test_date)->format('D, d M Y') }}</span></div>
    <div><strong>Time Slot</strong><span>{{ \Carbon\Carbon::parse($batch->start_time)->format('h:i A') }}</span></div>
    
    <div style="grid-column: span 4;"><strong>Address</strong><span>{{ $center->address ?? '—' }}</span></div>
</div>

<div class="summary">
    <span>CONSOLIDATED ROSTER: {{ count($attendees) }} CANDIDATES</span>
    <span>PRINTED ON: {{ now()->format('d M Y H:i') }}</span>
</div>

<table>
    <thead>
        <tr>
            <th style="width:30px; text-align:center">SR#</th>
            <th style="width:90px">ROLL NUMBER</th>
            <th>CANDIDATE NAME</th>
            <th>FATHER'S NAME</th>
            <th style="width:110px">CNIC</th>
            <th>POST APPLIED</th>
            <th style="width:70px; text-align:center">SIGNATURE</th>
            <th style="width:70px; text-align:center">PRESENT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendees as $i => $a)
        <tr>
            <td style="text-align:center">{{ $i + 1 }}</td>
            <td style="font-weight: bold; font-size: 11px;">{{ $a->roll_no ?? 'UNASSIGNED' }}</td>
            <td>{{ strtoupper($a->candidate_name) }}</td>
            <td>{{ strtoupper($a->father_name ?? '—') }}</td>
            <td>{{ $a->cnic }}</td>
            <td style="font-size: 9px;">{{ $a->job_title }}</td>
            <td><div class="sign-box"></div></td>
            <td><div class="sign-box"></div></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <div class="sign">
        <div style="margin-top: 40px;">Center Incharge Signature & Stamp</div>
    </div>
    <div class="sign">
        <div style="margin-top: 40px;">PATS Supervisor Signature</div>
    </div>
    <div class="sign">
        <div style="margin-top: 40px;">Chief Invigilator Signature</div>
    </div>
</div>

</body>
</html>
