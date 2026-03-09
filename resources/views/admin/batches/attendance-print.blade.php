<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Attendance Sheet — BATCH-{{ $batch->batch_number }}</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 15px; }
  @media print { body { padding: 0; } .no-print { display:none; } @page { size: A4; margin: 1cm; } }
  .page-header { border: 2px solid #000; padding: 8px 12px; margin-bottom: 8px; }
  .page-header h1 { font-size: 14px; font-weight: bold; text-align: center; margin: 0 0 3px; }
  .page-header h2 { font-size: 11px; text-align: center; margin: 0 0 6px; font-weight: normal; }
  .meta-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 4px; font-size: 10px; border-top: 1px solid #ccc; padding-top: 6px; margin-top: 6px; }
  .meta-item { display: flex; gap: 4px; }
  .meta-label { font-weight: bold; white-space: nowrap; }
  table { width: 100%; border-collapse: collapse; margin-top: 6px; }
  th { background: #333; color: #fff; padding: 5px 8px; text-align: left; font-size: 10px; }
  td { padding: 5px 8px; border: 1px solid #ccc; font-size: 10px; height: 22px; }
  .sig-col { width: 120px; }
  .num-col { width: 35px; text-align: center; }
  .roll-col { width: 90px; font-weight: bold; }
  .page-footer { margin-top: 12px; border: 1px solid #ccc; padding: 8px; }
  .sig-line { display: flex; gap: 30px; margin-top: 8px; }
  .sig-box { flex: 1; text-align: center; }
  .sig-box .line { border-top: 1px solid #000; margin-top: 25px; padding-top: 3px; font-size: 9px; }
  .page-break { page-break-after: always; }
  .btn-print { background:#0a3d62; color:#fff; border:none; padding:8px 20px; border-radius:6px; cursor:pointer; font-size:12px; margin-bottom:15px; }
</style>
</head>
<body>
<button class="btn-print no-print" onclick="window.print()">🖨 Print Attendance Sheet</button>

@php
  $pageSize = 25; // rows per page
  $pageNum  = 1;
@endphp

@foreach($summary as $jobId => $data)
  @php
    $chunks = $data['items']->chunk($pageSize);
    $totalPages = $chunks->count();
  @endphp
  @foreach($chunks as $chunkIndex => $candidates)
  <div>
    {{-- Page Header --}}
    <div class="page-header">
      <h1>PRIME ASSESSMENT &amp; TESTING SERVICES</h1>
      <h2>ATTENDANCE SHEET</h2>
      <div class="meta-grid">
        <div class="meta-item"><span class="meta-label">Project:</span> {{ $batch->project->name }}</div>
        <div class="meta-item"><span class="meta-label">Post:</span> {{ $data['job']->title }}</div>
        <div class="meta-item"><span class="meta-label">BPS:</span> {{ $data['job']->bps_grade ?? '—' }}</div>
        <div class="meta-item"><span class="meta-label">Test Center:</span> {{ $batch->center->name }}</div>
        <div class="meta-item"><span class="meta-label">City:</span> {{ $batch->center->city }}</div>
        <div class="meta-item"><span class="meta-label">TCID:</span> {{ $batch->center->tcid }}</div>
        <div class="meta-item"><span class="meta-label">Batch No:</span> BATCH-{{ $batch->batch_number }}</div>
        <div class="meta-item"><span class="meta-label">Test Date:</span> {{ $batch->test_date->format('d M Y') }}</div>
        <div class="meta-item"><span class="meta-label">Time:</span> {{ \Carbon\Carbon::parse($batch->start_time)->format('h:i A') }}</div>
        <div class="meta-item" style="grid-column:1/4"><span class="meta-label">Page:</span> {{ $pageNum++ }} of {{ $totalPages }}</div>
      </div>
    </div>

    {{-- Attendance Table --}}
    <table>
      <thead>
        <tr>
          <th class="num-col">Sr#</th>
          <th class="roll-col">Roll No.</th>
          <th>Candidate Name</th>
          <th>Father's Name</th>
          <th class="sig-col">Signature</th>
        </tr>
      </thead>
      <tbody>
        @foreach($candidates as $i => $rn)
        @php $app = $rn->application; $candidate = $app->candidate; $user = $candidate->user; @endphp
        <tr>
          <td class="num-col">{{ ($chunkIndex * $pageSize) + $i + 1 }}</td>
          <td class="roll-col">{{ $rn->roll_number }}</td>
          <td>{{ $user->full_name }}</td>
          <td>{{ $candidate->father_name }}</td>
          <td class="sig-col"></td>
        </tr>
        @endforeach
        {{-- Padding rows to fill page --}}
        @for($p = $candidates->count(); $p < $pageSize; $p++)
        <tr>
          <td class="num-col">{{ ($chunkIndex * $pageSize) + $p + 1 }}</td>
          <td class="roll-col"></td><td></td><td></td><td class="sig-col"></td>
        </tr>
        @endfor
      </tbody>
    </table>

    {{-- Page Footer --}}
    <div class="page-footer">
      <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:10px;">
        <span>Total Present: ________</span>
        <span>Total Absent: ________</span>
        <span>Total On Sheet: {{ $candidates->count() }}</span>
      </div>
      <div class="sig-line">
        <div class="sig-box"><div class="line">Invigilator Signature</div></div>
        <div class="sig-box"><div class="line">Supervisor Signature</div></div>
        <div class="sig-box"><div class="line">PATS Representative</div></div>
      </div>
    </div>
  </div>
  @if(!($loop->last) || !$loop->parent->last)
  <div class="page-break"></div>
  @endif
  @endforeach
@endforeach
</body>
</html>
