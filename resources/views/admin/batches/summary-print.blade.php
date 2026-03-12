<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Session Summary — #{{ $batch->id }}</title>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; line-height: 1.5; color: #1e293b; margin: 0; padding: 40px; }
        @media print { body { padding: 0; } .no-print { display: none; } }
        
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #066fd1; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-text { font-size: 24px; font-weight: 800; color: #066fd1; letter-spacing: -0.5px; }
        .document-type { font-size: 14px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-top: 4px; }
        
        .meta-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .meta-item { }
        .meta-label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; }
        .meta-value { font-size: 12px; font-weight: 600; color: #1e293b; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th { background: #f1f5f9; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 9px; text-align: left; padding: 12px 15px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        .fw-bold { font-weight: 700; }
        .text-blue { color: #066fd1; }
        
        .footer-signatures { display: grid; grid-template-columns: repeat(3, 1fr); gap: 60px; margin-top: 60px; }
        .sig-box { border-top: 1px solid #cbd5e1; padding-top: 10px; text-align: center; font-size: 10px; font-weight: 600; color: #64748b; }
        
        .print-footer { margin-top:100px; font-size: 8px; color: #94a3b8; display: flex; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 10px; }
        .btn-print { background: #066fd1; color: #fff; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; margin-bottom: 20px; transition: background 0.2s; }
        .btn-print:hover { background: #0559a8; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right;">
        <button class="btn-print" onclick="window.print()">Print Summary Report</button>
    </div>

    <div class="header">
        <div>
            <div class="logo-text">PATS</div>
            <div class="document-type">Session Summary Sheet</div>
        </div>
        <div style="text-align: right;">
            <div style="font-weight: 800; font-size: 18px; color: #1e293b;">#{{ $batch->id }}</div>
            <div style="font-size: 11px; color: #64748b;">Session No: {{ $batch->batch_number }}</div>
        </div>
    </div>

    <div class="meta-grid">
        <div class="meta-item">
            <div class="meta-label">Project</div>
            <div class="meta-value">{{ $batch->project->name }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Test Center</div>
            <div class="meta-value">{{ $batch->center->name }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">City</div>
            <div class="meta-value">{{ $batch->center->city->name }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Test Date</div>
            <div class="meta-value">{{ $batch->test_date->format('l, d M Y') }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Reporting Time</div>
            <div class="meta-value">{{ date('h:i A', strtotime($batch->reporting_time)) }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Start Time</div>
            <div class="meta-value">{{ date('h:i A', strtotime($batch->start_time)) }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Total Allocated</div>
            <div class="meta-value text-blue">{{ $batch->booked_seats }} Candidates</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Envelope Size</div>
            <div class="meta-value">{{ $batch->envelope_size }} / Env</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Job Post / Category</th>
                <th>Roll Number From</th>
                <th>Roll Number To</th>
                <th style="text-align: center;">Total Count</th>
                <th style="text-align: center;">Est. Envelopes</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($summary as $row)
            @php 
                $grandTotal += $row->count; 
                $envs = ceil($row->count / $batch->envelope_size);
            @endphp
            <tr>
                <td class="fw-bold">{{ $row->job->title }}</td>
                <td class="text-blue fw-bold">{{ $row->roll_from }}</td>
                <td class="text-blue fw-bold">{{ $row->roll_to }}</td>
                <td style="text-align: center;">{{ $row->count }}</td>
                <td style="text-align: center;">{{ $envs }}</td>
            </tr>
            @endforeach
            <tr style="background: #f8fafc;">
                <td colspan="3" class="fw-bold" style="text-align: right; padding-right: 30px;">GRAND TOTAL</td>
                <td style="text-align: center;" class="fw-bold text-blue">{{ $grandTotal }}</td>
                <td style="text-align: center;" class="fw-bold">{{ ceil($grandTotal / $batch->envelope_size) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-signatures">
        <div class="sig-box">Center Superintendent</div>
        <div class="sig-box">PATS Representative</div>
        <div class="sig-box">Date & Official Stamp</div>
    </div>

    <div class="print-footer">
        <div>Generated by PATS Management System on {{ now()->format('d M Y, H:i') }}</div>
        <div>Page 1 of 1</div>
    </div>
</body>
</html>
