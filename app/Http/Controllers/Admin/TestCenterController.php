<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreCenterRequest;
use App\Http\Requests\UpdateCenterRequest;
use App\Http\Controllers\Controller;
use App\Models\TestCenter;
use Illuminate\Http\Request;

class TestCenterController extends Controller
{
    public function index()
    {
        $cities = \App\Models\City::with(['testCenters' => function($q) {
            $q->orderBy('priority_order')->orderBy('name');
        }])->whereHas('testCenters')->orderBy('name')->get();

        return view('admin.centers.index', compact('cities'));
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:test_centers,id'
        ]);

        foreach ($data['order'] as $index => $id) {
            TestCenter::where('id', $id)->update(['priority_order' => $index + 1]);
        }
        
        return response()->json(['success' => true]);
    }

    public function create() 
    { 
        $cities = \App\Models\City::orderBy('name')->get();
        return view('admin.centers.create', compact('cities')); 
    }

    public function store(StoreCenterRequest $request)
    {
        $data = $request->validated();
        TestCenter::create($data);
        return redirect()->route('admin.centers.index')->with('success', 'Test Center added.');
    }

    public function edit(TestCenter $center) 
    { 
        $cities = \App\Models\City::orderBy('name')->get();
        return view('admin.centers.edit', compact('center', 'cities')); 
    }

    public function update(UpdateCenterRequest $request, TestCenter $center)
    {
        $data = $request->validated();
        $center->update($data);
        return redirect()->route('admin.centers.index')->with('success', 'Test Center updated.');
    }

    public function destroy(TestCenter $center)
    {
        if ($center->batches()->exists()) {
            return back()->with('error', 'Cannot delete test center with associated exam sessions/batches.');
        }

        $center->delete();
        return redirect()->route('admin.centers.index')->with('success', 'Test Center deleted.');
    }
}
