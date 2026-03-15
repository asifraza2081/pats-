<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\TestCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display the attendance sheet selection screen.
     */
    public function index(Request $request)
    {
        $centerId = (int) $request->get('center_id', 0);
        $slotDate = $request->get('slot_date', '');
        $slotId   = (int) $request->get('slot_id', 0);

        if ($slotDate !== '') {
            try {
                $slotDate = Carbon::parse($slotDate)->toDateString();
            } catch (\Exception $e) {
                $slotDate = '';
            }
        }

        // Fetch all active centers for the selection dropdown
        $centers = TestCenter::where('is_active', true)
            ->join('cities', 'test_centers.city_id', '=', 'cities.id')
            ->select('test_centers.*')
            ->orderBy('cities.name')
            ->orderBy('test_centers.name')
            ->get();

        $dates = [];
        if ($centerId) {
            $dates = Batch::where('center_id', $centerId)
                ->selectRaw('DISTINCT test_date')
                ->orderBy('test_date', 'asc')
                ->get()
                ->pluck('test_date')
                ->map(fn($d) => $d instanceof \Carbon\Carbon ? $d->toDateString() : (string) $d);
        }

        $slots = [];
        if ($centerId && $slotDate) {
            $slots = Batch::with(['project'])
                ->where('center_id', $centerId)
                ->whereDate('test_date', $slotDate)
                ->orderBy('start_time', 'asc')
                ->get();
        }

        $attendees = [];
        $slotInfo  = null;
        $center    = null;

        if ($slotId) {
            $slotInfo = Batch::with(['project', 'center.city'])->find($slotId);
            if ($slotInfo) {
                $center    = $slotInfo->center;
                $attendees = DB::table('applications as a')
                    ->join('candidates as c', 'c.id', '=', 'a.candidate_id')
                    ->join('users as u', 'u.id', '=', 'c.user_id')
                    ->join('pats_jobs as j', 'j.id', '=', 'a.job_id')
                    ->leftJoin('exam_rollnos as rn', 'rn.application_id', '=', 'a.id')
                    ->where('a.batch_id', $slotId)
                    ->select([
                        'rn.roll_no',
                        DB::raw("CONCAT(u.first_name, ' ', u.last_name) as candidate_name"),
                        'c.father_name',
                        'u.cnic',
                        'j.title as job_title',
                        'a.status',
                        'a.id as app_id'
                    ])
                    ->orderBy('rn.roll_no', 'asc')
                    ->orderBy('u.first_name', 'asc')
                    ->get();
            }
        }

        return view('admin.attendance.index', compact(
            'centers', 'centerId', 'slotDate', 'slotId',
            'dates', 'slots', 'attendees', 'slotInfo', 'center'
        ));
    }

    /**
     * Render the printable attendance sheet.
     */
    public function print(Request $request, Batch $batch)
    {
        $center = $batch->center;
        $attendees = DB::table('applications as a')
            ->join('candidates as c', 'c.id', '=', 'a.candidate_id')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->join('pats_jobs as j', 'j.id', '=', 'a.job_id')
            ->join('projects as p', 'p.id', '=', 'j.project_id')
            ->leftJoin('exam_rollnos as rn', 'rn.application_id', '=', 'a.id')
            ->where('a.batch_id', $batch->id)
            ->select([
                'rn.roll_no',
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as candidate_name"),
                'c.father_name',
                'u.cnic',
                'j.title as job_title',
                'a.status',
                'a.id as app_id',
                'p.name as project_name'
            ])
            ->orderBy('rn.roll_no', 'asc')
            ->orderBy('u.first_name', 'asc')
            ->get();

        return view('admin.attendance.print', compact('batch', 'center', 'attendees'));
    }
}
