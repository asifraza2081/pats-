<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\Project;
use App\Models\TestCenter;
use App\Models\CenterSlot;

class TestCenterController
{
    public function __construct()
    {
        Auth::requireRole('super_admin', 'admin');
    }

    public function index(Request $request, array $params = []): void
    {
        $centers = TestCenter::all();
        View::render('admin/centers/index', ['pageTitle' => 'Test Centers', 'centers' => $centers], 'admin');
    }

    public function create(Request $request, array $params = []): void
    {
        View::render('admin/centers/create', ['pageTitle' => 'Add Test Center'], 'admin');
    }

    public function store(Request $request, array $params = []): void
    {
        CSRF::check();

        $data = $request->only('name', 'city', 'province', 'address', 'map_url', 'is_active');
        $v = Validator::make($data, [
            'name'     => 'required|max:150',
            'city'     => 'required|max:80',
            'province' => 'required|max:80',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('old', $data);
            Response::redirect('/admin/centers/create');
        }

        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        TestCenter::create($data);

        Session::flash('success', 'Test center created.');
        Response::redirect('/admin/centers');
    }

    public function edit(Request $request, array $params = []): void
    {
        $center = TestCenter::find((int) $params['id']);
        if (!$center) Response::abort(404);

        View::render('admin/centers/edit', ['pageTitle' => 'Edit Center', 'center' => $center], 'admin');
    }

    public function update(Request $request, array $params = []): void
    {
        CSRF::check();
        $id = (int)$params['id'];
        
        $data = $request->only('name', 'city', 'province', 'address', 'map_url');
        $data['is_active'] = $request->post('is_active') ? 1 : 0;

        TestCenter::updateWhere($data, ['id' => $id]);
        Session::flash('success', 'Test center updated.');
        Response::redirect('/admin/centers');
    }

    public function slots(Request $request, array $params = []): void
    {
        $centerId = (int) $params['id'];
        $center = TestCenter::find($centerId);
        if (!$center) Response::abort(404);

        $projects = Project::where(['status' => 'open']);
        
        $db = \App\Core\Database::getInstance();
        $slots = $db->fetchAll(
            'SELECT s.*, p.name as project_name 
             FROM center_slots s
             JOIN projects p ON p.id = s.project_id
             WHERE s.center_id = ?
             ORDER BY s.slot_date DESC, s.slot_time DESC',
            [$centerId]
        );

        View::render('admin/centers/slots', [
            'pageTitle' => 'Manage Slots: ' . $center['name'],
            'center'    => $center,
            'projects'  => $projects,
            'slots'     => $slots
        ], 'admin');
    }

    public function storeSlot(Request $request, array $params = []): void
    {
        CSRF::check();
        $centerId = (int) $params['id'];

        $data = $request->only('project_id', 'slot_date', 'slot_time', 'total_seats');
        $v = Validator::make($data, [
            'project_id'  => 'required|numeric',
            'slot_date'   => 'required|date',
            'slot_time'   => 'required',
            'total_seats' => 'required|numeric|min:1',
        ]);

        if ($v->fails()) {
            Session::flash('error', 'Please fill all fields correctly.');
            Response::redirect("/admin/centers/{$centerId}/slots");
        }

        $data['center_id'] = $centerId;
        $data['booked_seats'] = 0;

        CenterSlot::create($data);
        
        // Link project and center if not already linked
        TestCenter::assignToProject((int)$data['project_id'], $centerId);

        Session::flash('success', 'Time slot added and center mapped to project.');
        Response::redirect("/admin/centers/{$centerId}/slots");
    }

    public function deleteSlot(Request $request, array $params = []): void
    {
        CSRF::check();
        $slotId = (int) $params['id'];
        $slot = CenterSlot::find($slotId);

        if ($slot['booked_seats'] > 0) {
            Session::flash('error', 'Cannot delete a slot that already has booked candidates.');
            Response::redirect("/admin/centers/{$slot['center_id']}/slots");
        }

        CenterSlot::deleteWhere(['id' => $slotId]);
        Session::flash('success', 'Slot deleted successfully.');
        Response::redirect("/admin/centers/{$slot['center_id']}/slots");
    }
}
