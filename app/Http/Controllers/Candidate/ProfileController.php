<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\EducationHistory;
use App\Models\WorkExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    private function candidate(): Candidate
    {
        return Auth::user()->candidate ?? abort(404);
    }

    public function show()
    {
        $candidate = $this->candidate();
        $education = $candidate->education()->orderBy('degree_level', 'desc')->get();
        $experience = $candidate->experience()->orderBy('from_date', 'desc')->get();
        return view('candidate.profile', compact('candidate', 'education', 'experience'));
    }

    public function update(Request $request)
    {
        $candidate = $this->candidate();

        if ($candidate->profile_locked) {
            return back()->with('error', 'Your profile is locked because you have submitted applications.');
        }

        $data = $request->validate([
            'father_name'           => 'required|string|max:120',
            'dob'                   => 'required|date|before:today',
            'gender'                => 'required|in:Male,Female',
            'marital_status'        => 'required|in:Single,Married,Divorced,Widowed',
            'religion'              => 'required|in:Islam,Christianity,Hinduism,Sikhism,Other',
            'blood_group'           => 'nullable|string|max:5',
            'current_occupation'    => 'nullable|string|max:120',
            'disability'            => 'nullable|boolean',
            'disability_type'       => 'nullable|string|max:120',
            'province_of_domicile'  => 'required|string|max:80',
            'district_of_domicile'  => 'required|string|max:80',
            'permanent_address'     => 'required|string',
            'postal_address'        => 'required|string',
            'same_postal_address'   => 'nullable|boolean',
            'alternate_phone'       => 'nullable|string|max:15',
        ]);

        // Ensure disability is always boolean (false when checkbox unchecked)
        $data['disability'] = $request->boolean('disability');


        // Handle same postal address
        if ($request->boolean('same_postal_address')) {
            $data['postal_address'] = $data['permanent_address'];
        }

        // Photo upload
        if ($request->hasFile('photo')) {
            $request->validate(['photo' => 'image|max:5120']);
            if ($candidate->photo_path) Storage::delete($candidate->photo_path);
            $data['photo_path'] = $request->file('photo')->store('photos', 'public');
        }

        // CNIC copy upload
        if ($request->hasFile('cnic_copy')) {
            $request->validate(['cnic_copy' => 'image|max:5120']);
            if ($candidate->cnic_front_path) Storage::delete($candidate->cnic_front_path);
            $data['cnic_front_path'] = $request->file('cnic_copy')->store('cnic_copies', 'public');
        }

        $candidate->update($data);
        return back()->with('success', 'Profile updated successfully.');
    }

    // ── Education ─────────────────────────────────────────────
    public function addEducation(Request $request)
    {
        $candidate = $this->candidate();
        $data = $request->validate([
            'degree_level'   => 'required|integer|min:1|max:6',
            'degree_name'    => 'required|string|max:100',
            'subject_major'  => 'nullable|string|max:100',
            'institution'    => 'nullable|string|max:150',
            'passing_year'   => 'nullable|integer|min:1970|max:' . date('Y'),
            'marks_type'     => 'required|in:Marks,CGPA',
            'obtained_marks' => 'nullable|numeric|min:0',
            'total_marks'    => 'nullable|numeric|min:0',
        ]);
        $candidate->education()->create($data);
        return back()->with('success', 'Education record added.');
    }

    public function updateEducation(Request $request, EducationHistory $edu)
    {
        abort_if($edu->candidate_id !== $this->candidate()->id, 403);
        $data = $request->validate([
            'degree_level'   => 'required|integer|min:1|max:6',
            'degree_name'    => 'required|string|max:100',
            'subject_major'  => 'nullable|string|max:100',
            'institution'    => 'nullable|string|max:150',
            'passing_year'   => 'nullable|integer|min:1970|max:' . date('Y'),
            'marks_type'     => 'required|in:Marks,CGPA',
            'obtained_marks' => 'nullable|numeric|min:0',
            'total_marks'    => 'nullable|numeric|min:0',
        ]);
        $edu->update($data);
        return back()->with('success', 'Education record updated.');
    }

    public function deleteEducation(EducationHistory $edu)
    {
        abort_if($edu->candidate_id !== $this->candidate()->id, 403);
        abort_if($this->candidate()->profile_locked, 403, 'Profile locked.');
        $edu->delete();
        return back()->with('success', 'Education record removed.');
    }

    // ── Work Experience ───────────────────────────────────────
    public function addExperience(Request $request)
    {
        $candidate = $this->candidate();
        $data = $request->validate([
            'job_type'          => 'required|in:Public,Private',
            'organization_name' => 'required|string|max:150',
            'designation'       => 'required|string|max:120',
            'from_date'         => 'required|date',
            'to_date'           => 'nullable|date|after:from_date',
            'is_current'        => 'nullable|boolean',
        ]);
        if ($request->boolean('is_current')) $data['to_date'] = null;
        $candidate->experience()->create($data);
        return back()->with('success', 'Experience record added.');
    }

    public function updateExperience(Request $request, WorkExperience $exp)
    {
        abort_if($exp->candidate_id !== $this->candidate()->id, 403);
        $data = $request->validate([
            'job_type'          => 'required|in:Public,Private',
            'organization_name' => 'required|string|max:150',
            'designation'       => 'required|string|max:120',
            'from_date'         => 'required|date',
            'to_date'           => 'nullable|date|after:from_date',
            'is_current'        => 'nullable|boolean',
        ]);
        if ($request->boolean('is_current')) $data['to_date'] = null;
        $exp->update($data);
        return back()->with('success', 'Experience record updated.');
    }

    public function deleteExperience(WorkExperience $exp)
    {
        abort_if($exp->candidate_id !== $this->candidate()->id, 403);
        abort_if($this->candidate()->profile_locked, 403, 'Profile locked.');
        $exp->delete();
        return back()->with('success', 'Experience record removed.');
    }
}
