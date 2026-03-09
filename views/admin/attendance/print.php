<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Sheet — <?= \App\Core\View::e($center['name'] ?? '') ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; padding: 20px; }
        .header { text-align: center; padding-bottom: 12px; border-bottom: 2px solid #000; margin-bottom: 14px; }
        .header h2 { font-size: 15px; font-weight: bold; letter-spacing: 0.5px; }
        .header p  { font-size: 11px; color: #444; margin-top: 3px; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 4px; margin-bottom: 14px; padding: 10px; border: 1px solid #ccc; background: #f9f9f9; }
        .meta-grid div { }
        .meta-grid strong { display: block; font-size: 9px; text-transform: uppercase; color: #777; letter-spacing: 0.3px; }
        .meta-grid span { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #222; color: #fff; padding: 6px 8px; font-size: 10px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #ddd; vertical-align: middle; }
        tr:nth-child(even) { background: #f5f5f5; }
        .sign-box { width: 70px; height: 25px; border: 1px solid #aaa; display: inline-block; }
        .footer { margin-top: 30px; border-top: 1px solid #999; padding-top: 10px; display: flex; justify-content: space-between; }
        .footer .sign { text-align: center; }
        .footer .sign div { margin-top: 30px; border-top: 1px solid #000; font-size: 10px; padding-top: 2px; }
        .summary { margin: 10px 0; font-size: 11px; }
        .summary span { margin-right: 20px; }
        @media print {
            body { padding: 10px; }
            .no-print { display: none !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:right; margin-bottom: 10px;">
    <button onclick="window.print()" style="padding: 6px 16px; background:#1a56db; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:12px;">
        🖨️ Print
    </button>
    <button onclick="window.close()" style="margin-left:8px; padding: 6px 16px; background:#e5e7eb; color:#111; border:none; border-radius:4px; cursor:pointer; font-size:12px;">
        ✕ Close
    </button>
</div>

<div class="header">
    <h2>PRIME ASSESSMENT &amp; TESTING SERVICES (PATS)</h2>
    <p>ATTENDANCE SHEET</p>
</div>

<div class="meta-grid">
    <div><strong>Test Center</strong><span><?= \App\Core\View::e($center['name'] ?? 'N/A') ?></span></div>
    <div><strong>City</strong><span><?= \App\Core\View::e($center['city'] ?? '') ?></span></div>
    <div><strong>Province</strong><span><?= \App\Core\View::e($center['province'] ?? '') ?></span></div>
    <div><strong>Address</strong><span><?= \App\Core\View::e($center['address'] ?? '—') ?></span></div>
    <div><strong>Test Date</strong><span><?= date('D, d F Y', strtotime($slotInfo['slot_date'])) ?></span></div>
    <div><strong>Time Slot</strong><span><?= date('h:i A', strtotime($slotInfo['slot_time'])) ?></span></div>
    <div><strong>Total Booked</strong><span><?= $slotInfo['booked_seats'] ?> / <?= $slotInfo['total_seats'] ?> seats</span></div>
    <div><strong>Printed On</strong><span><?= date('d M Y H:i') ?></span></div>
</div>

<?php if (!empty($attendees)): ?>
<div class="summary">
    <span><strong>Total Candidates:</strong> <?= count($attendees) ?></span>
    <span><strong>Project:</strong> <?= \App\Core\View::e($attendees[0]['project_name'] ?? '—') ?></span>
</div>
<table>
    <thead>
        <tr>
            <th style="width:30px">#</th>
            <th style="width:90px">Roll Number</th>
            <th>Candidate Name</th>
            <th>Father's Name</th>
            <th style="width:120px">CNIC</th>
            <th>Post Applied</th>
            <th style="width:80px; text-align:center">Present</th>
            <th style="width:80px; text-align:center">Signature</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($attendees as $i => $a): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><strong><?= \App\Core\View::e($a['roll_number'] ?? 'N/A') ?></strong></td>
            <td><?= \App\Core\View::e($a['candidate_name']) ?></td>
            <td><?= \App\Core\View::e($a['father_name'] ?? '—') ?></td>
            <td><?= \App\Core\View::e($a['cnic']) ?></td>
            <td><?= \App\Core\View::e($a['job_title']) ?></td>
            <td style="text-align:center">
                <div class="sign-box"></div>
            </td>
            <td style="text-align:center">
                <div class="sign-box"></div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p style="text-align:center; padding: 30px; color: #888;">No candidates scheduled for this slot.</p>
<?php endif; ?>

<div class="footer">
    <div class="sign">
        <div>Center Incharge Signature</div>
    </div>
    <div class="sign">
        <div>Supervisor Signature</div>
    </div>
    <div class="sign">
        <div>PATS Officer Signature</div>
    </div>
</div>

</body>
</html>
