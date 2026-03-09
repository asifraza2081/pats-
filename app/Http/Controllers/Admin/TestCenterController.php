<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestCenter;
use Illuminate\Http\Request;

class TestCenterController extends Controller
{
    public function index()
    {
        $centers = TestCenter::latest()->get();
        return view('admin.centers.index', compact('centers'));
    }

    public function create() { return view('admin.centers.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tcid'           => 'required|string|max:10|unique:test_centers,tcid',
            'name'           => 'required|string|max:150',
            'city'           => 'required|string|max:80',
            'province'       => 'required|string|max:80',
            'total_capacity' => 'required|integer|min:1',
            'address'        => 'required|string',
            'map_url'        => 'nullable|url',
        ]);
        TestCenter::create($data);
        return redirect()->route('admin.centers.index')->with('success', 'Test Center added.');
    }

    public function edit(TestCenter $center) { return view('admin.centers.edit', compact('center')); }

    public function update(Request $request, TestCenter $center)
    {
        $data = $request->validate([
            'tcid'           => "required|string|max:10|unique:test_centers,tcid,{$center->id}",
            'name'           => 'required|string|max:150',
            'city'           => 'required|string|max:80',
            'province'       => 'required|string|max:80',
            'total_capacity' => 'required|integer|min:1',
            'address'        => 'required|string',
            'map_url'        => 'nullable|url',
        ]);
        $center->update($data);
        return redirect()->route('admin.centers.index')->with('success', 'Test Center updated.');
    }

    public function destroy(TestCenter $center)
    {
        $center->delete();
        return redirect()->route('admin.centers.index')->with('success', 'Test Center deleted.');
    }
}
