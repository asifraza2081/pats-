<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestCenter;
use Illuminate\Http\Request;

class TestCenterController extends Controller
{
    public function index()
    {
        $centers = TestCenter::with('city')->latest()->paginate(15);
        return view('admin.centers.index', compact('centers'));
    }

    public function create() 
    { 
        $cities = \App\Models\City::orderBy('name')->get();
        return view('admin.centers.create', compact('cities')); 
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tcid'             => 'required|string|max:10|unique:test_centers,tcid',
            'name'             => 'required|string|max:150',
            'city_id'          => 'required|exists:cities,id',
            'seating_capacity' => 'required|integer|min:1',
            'address'          => 'required|string',
            'map_url'          => 'nullable|url',
        ]);
        TestCenter::create($data);
        return redirect()->route('admin.centers.index')->with('success', 'Test Center added.');
    }

    public function edit(TestCenter $center) 
    { 
        $cities = \App\Models\City::orderBy('name')->get();
        return view('admin.centers.edit', compact('center', 'cities')); 
    }

    public function update(Request $request, TestCenter $center)
    {
        $data = $request->validate([
            'tcid'             => "required|string|max:10|unique:test_centers,tcid,{$center->id}",
            'name'             => 'required|string|max:150',
            'city_id'          => 'required|exists:cities,id',
            'seating_capacity' => 'required|integer|min:1',
            'address'          => 'required|string',
            'map_url'          => 'nullable|url',
            'is_active'        => 'boolean',
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
