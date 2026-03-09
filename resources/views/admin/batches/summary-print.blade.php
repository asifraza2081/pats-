<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Batch Summary — BATCH-{{ $batch->batch_number }}</title>
<style>
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111; margin: 0; padding: 20px; }
  @media print { body { padding: 8px; } .no-print { display:none; } }
  h1 { font-size: 16px; color: #0a3d62; margin: 0 0 2px; }
  h2 { font-size: 12px; color: #444; margin: 0 0 8px; font-weight: normal; }
  .header { border-bottom: 2px solid #0a3d62; padding-bottom: 8px; margin-bottom: 12px; }
  .meta { display: flex; gap: 30px; flex-wrap: wrap; margin-bottom: 12px; }
  .meta-item { }
  .meta-item .lbl { color: #888; font-size: 9px; text-transform: uppercase; }
  .meta-item .val { font-weight: bold; font-size: 12px; color: #0a3d62; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th { background: #0a3d62; color: #fff; padding: 6px 10px; text-align: left; font-size: 10px; }
  td { padding: 5px 10px; border-bottom: 1px solid #eee; font-size: 10px; }
  tr:nth-child(even) { background: #f9f9f9; }
  .env-row td { background: #eef4fb; font-weight: bold; color: #0a3d62; }
  .total-row td { background: #0a3d62; color: #fff; font-weight: bold; }
  .footer { margin-top: 20px; border-top: 1px solid #ccc; padding-top: 8px; font-size: 9px; color: #666; display: flex; justify-content: space-between; }
  .btn-print { background:#0a3d62;color:#fff;border:none;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:12px;margin-bottom:15px; }
</style>
</head>
<body>
<button class="btn-print no-print" onclick="window.print()">🖨 Print / Save PDF</button>
<div class="header">
  <h1>PRIME ASSESSMENT &amp; TESTING SERVICES</h1>
  <h2>Batch Summary Sheet — {{ $batch->project->org_name }}</h2>
</div>
<div class="meta">
  <div class="meta-item"><div class="lbl">Project</div><div class="val">{{ $batch->project->name }}</div></div>
  <div class="meta-item"><div class="lbl">Batch No.</div><div class="val">BATCH-{{ $batch->batch_number }}</div></div>
  <div class="meta-item"><div class="lbl">Test Center</div><div class="val">{{ $batch->center->name }}</div></div>
  <div class="meta-item"><div class="lbl">TCID</div><div class="val">{{ $batch->center->tcid }}</div></div>
  <div class="meta-item"><div class="lbl">City</div><div class="val">{{ $batch->center->city }}</div></div>
  <div class="meta-item"><div class="lbl">Test Date</div><div class="val">{{ $batch->test_date->format('d M Y') }}</div></div>
  <div class="meta-item"><div class="lbl">Reporting Time</div><div class="val">{{ \Carbon\Carbon::parse($batch->reporting_time)->format('h:i A') }}</div></div>
  <div class="meta-item"><div class="lbl">Start Time</div><div class="val">{{ \Carbon\Carbon::parse($batch->start_time)->format('h:i A') }}</div></div>
  <div class="meta-item"><div class="lbl">Envelope Size</div><div class="val">{{ $batch->envelope_size }}</div></div>
</div>

<table>
  <thead>
    <tr>
      <th>Env#</th>
      <th>Job Type / Post</th>
      <th>Roll No. From</th>
      <th>Roll No. To</th>
      <th>Count</th>
    </tr>
  </thead>
  <tbody>
    @php $envNum = 1; $grandTotal = 0; @endphp
    @foreach($summary as $jobId => $data)
      @php
        $totalInJob = $data['count'];
        $envSize = $batch->envelope_size;
        $fullEnvelopes = intdiv($totalInJob, $envSize);
        $remainder = $totalInJob % $envSize;
        $items = $data['items'];
        $cursor = 0;
        $grandTotal += $totalInJob;
      @endphp
      @for($e = 0; $e < ($fullEnvelopes + ($remainder > 0 ? 1 : 0)); $e++)
        @php
          $count = ($e < $fullEnvelopes) ? $envSize : $remainder;
          $from = $items[$cursor]->roll_number;
          $to   = $items[$cursor + $count - 1]->roll_number;
          $cursor += $count;
        @endphp
        <tr class="env-row">
          <td>ENV-{{ str_pad($envNum++, 3, '0', STR_PAD_LEFT) }}</td>
          <td>{{ $data['job']->title }}{{ $data['job']->bps_grade ? ' ('..$data['job']->bps_grade.')' : '' }}</td>
          <td>{{ $from }}</td>
          <td>{{ $to }}</td>
          <td>{{ $count }}</td>
        </tr>
      @endfor
    @endforeach
    <tr class="total-row">
      <td colspan="4">TOTAL CANDIDATES IN BATCH</td>
      <td>{{ $grandTotal }}</td>
    </tr>
  </tbody>
</table>

<div style="margin-top:30px; display:flex; gap:40px;">
  <div style="text-align:center;min-width:150px">
    <div style="border-top:1px solid #000;padding-top:4px;font-size:10px">Center In-Charge</div>
  </div>
  <div style="text-align:center;min-width:150px">
    <div style="border-top:1px solid #000;padding-top:4px;font-size:10px">PATS Representative</div>
  </div>
  <div style="text-align:center;min-width:150px">
    <div style="border-top:1px solid #000;padding-top:4px;font-size:10px">Date</div>
  </div>
</div>

<div class="footer">
  <span>Generated: {{ now()->format('d M Y H:i') }}</span>
  <span>PATS — Confidential</span>
</div>
</body>
</html>
