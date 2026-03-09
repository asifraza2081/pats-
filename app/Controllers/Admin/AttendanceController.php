<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\TestCenter;
use App\Models\CenterSlot;

class AttendanceController
{
    public function __construct()
    {
        Auth::requireRole('super_admin', 'admin', 'data_entry');
    }

    /**
     * Show the attendance sheet selector form.
     */
    public function index(Request $request, array $params = []): void
    {
        $db = Database::getInstance();

        $centerId  = (int)($request->get('center_id') ?? 0);
        $slotDate  = $request->get('slot_date', '');
        $slotId    = (int)($request->get('slot_id') ?? 0);

        $centers = TestCenter::all('city ASC, name ASC');

        // Get distinct dates for the selected center
        $dates = [];
        if ($centerId) {
            $dates = $db->fetchAll(
                'SELECT DISTINCT slot_date FROM center_slots WHERE center_id = ? ORDER BY slot_date ASC',
                [$centerId]
            );
        }

        // Get time slots for selected center + date
        $slots = [];
        if ($centerId && $slotDate) {
            $slots = $db->fetchAll(
                'SELECT cs.*, p.name as project_name
                 FROM center_slots cs
                 JOIN projects p ON p.id = cs.project_id
                 WHERE cs.center_id = ? AND cs.slot_date = ?
                 ORDER BY cs.slot_time ASC',
                [$centerId, $slotDate]
            );
        }

        // Get attendance list for selected slot
        $attendees = [];
        $slotInfo  = null;
        $center    = null;
        if ($slotId) {
            $slotInfo = CenterSlot::find($slotId);
            if ($slotInfo) {
                $center = TestCenter::find((int)$slotInfo['center_id']);
                $attendees = $db->fetchAll(
                    'SELECT rn.roll_number, u.name as candidate_name, c.father_name, u.cnic,
                            j.title as job_title, a.status, a.id as app_id
                     FROM applications a
                     JOIN candidates c ON c.id = a.candidate_id
                     JOIN users u ON u.id = c.user_id
                     JOIN jobs j ON j.id = a.job_id
                     LEFT JOIN roll_numbers rn ON rn.application_id = a.id
                     WHERE a.slot_id = ?
                     ORDER BY rn.roll_number ASC, u.name ASC',
                    [$slotId]
                );
            }
        }

        View::render('admin/attendance/index', [
            'pageTitle' => 'Attendance Sheets',
            'centers'   => $centers,
            'centerId'  => $centerId,
            'slotDate'  => $slotDate,
            'slotId'    => $slotId,
            'dates'     => $dates,
            'slots'     => $slots,
            'attendees' => $attendees,
            'slotInfo'  => $slotInfo,
            'center'    => $center,
        ], 'admin');
    }

    /**
     * Render a printable HTML attendance sheet (opens in new tab for browser print).
     */
    public function print(Request $request, array $params = []): void
    {
        Auth::requireRole('super_admin', 'admin', 'data_entry');
        $slotId = (int)$params['slotId'];

        $db       = Database::getInstance();
        $slotInfo = CenterSlot::find($slotId);
        if (!$slotInfo) Response::abort(404);

        $center = TestCenter::find((int)$slotInfo['center_id']);

        $db2 = Database::getInstance();
        $attendees = $db2->fetchAll(
            'SELECT rn.roll_number, u.name as candidate_name, c.father_name, u.cnic,
                    j.title as job_title, a.status, a.id as app_id,
                    p.name as project_name
             FROM applications a
             JOIN candidates c ON c.id = a.candidate_id
             JOIN users u ON u.id = c.user_id
             JOIN jobs j ON j.id = a.job_id
             JOIN projects p ON p.id = j.project_id
             LEFT JOIN roll_numbers rn ON rn.application_id = a.id
             WHERE a.slot_id = ?
             ORDER BY rn.roll_number ASC, u.name ASC',
            [$slotId]
        );

        View::render('admin/attendance/print', [
            'pageTitle' => 'Attendance Sheet — ' . date('d M Y', strtotime($slotInfo['slot_date'])),
            'slotInfo'  => $slotInfo,
            'center'    => $center,
            'attendees' => $attendees,
        ]);
        // No layout wrapper — this is a standalone print page
    }
}
