<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::withCount('testCenters')->paginate(25);
        return view('admin.cities.index', compact('cities'));
    }

    public function create()
    {
        return view('admin.cities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100|unique:cities,name',
            'province'       => 'required|string|max:50',
            'is_test_center' => 'boolean',
        ]);

        City::create($data);

        return redirect()->route('admin.cities.index')->with('success', 'City added successfully.');
    }

    public function edit(City $city)
    {
        return view('admin.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100|unique:cities,name,' . $city->id,
            'province'       => 'required|string|max:50',
            'is_test_center' => 'boolean',
        ]);

        $city->update($data);

        return redirect()->route('admin.cities.index')->with('success', 'City updated.');
    }

    public function destroy(City $city)
    {
        if ($city->testCenters()->count() > 0) {
            return back()->with('error', 'Cannot delete city with associated test centers.');
        }
        $city->delete();
        return redirect()->route('admin.cities.index')->with('success', 'City deleted.');
    }
}
