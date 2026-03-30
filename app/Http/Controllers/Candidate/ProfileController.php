<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\EducationRequest;
use App\Http\Requests\ExperienceRequest;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\City;
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
        $cities = City::orderBy('name')->get();
        return view('candidate.profile', compact('candidate', 'education', 'experience', 'cities'));
    }

    public function viewProfile()
    {
        $candidate = $this->candidate()->load(['education', 'experience', 'user', 'domicileCity', 'addressCity']);
        return view('candidate.profile-bio', compact('candidate'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $candidate = $this->candidate();
        $this->authorize('update', $candidate);

        $data = $request->validated();

        // Handle same postal address
        if ($data['same_postal_address']) {
            $data['postal_address'] = $data['permanent_address'];
        }

        // Photo upload
        if ($request->hasFile('photo')) {
            if ($candidate->photo_path) Storage::delete($candidate->photo_path);
            $data['photo_path'] = $request->file('photo')->store('photos', 'public');
        }

        // CNIC copy upload
        if ($request->hasFile('cnic_copy')) {
            $data['cnic_front_path'] = $request->file('cnic_copy')->store('cnic_copies', 'public');
        }

        // Unset fields not in Candidate::$fillable to avoid strict mode MassAssignmentException
        unset($data['photo'], $data['cnic_copy'], $data['cnic']);

        $candidate->update($data);

        // Update core user fields if not locked
        if (!$candidate->profile_locked) {
            $user = auth()->user();
            $user->update([
                'cnic' => $data['cnic'] ?? $user->cnic,
            ]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    // ── Education ─────────────────────────────────────────────
    public function addEducation(EducationRequest $request)
    {
        $candidate = $this->candidate();
        $this->authorize('update', $candidate);
        $data = $request->validated();
        $candidate->education()->create($data);
        return back()->with('success', 'Education record added.');
    }

    public function updateEducation(EducationRequest $request, EducationHistory $edu)
    {
        $this->authorize('update', $edu);
        $data = $request->validated();
        $edu->update($data);
        return back()->with('success', 'Education record updated.');
    }

    public function deleteEducation(EducationHistory $edu)
    {
        $this->authorize('delete', $edu);
        $edu->delete();
        return back()->with('success', 'Education record removed.');
    }

    // ── Work Experience ───────────────────────────────────────
    public function addExperience(ExperienceRequest $request)
    {
        $candidate = $this->candidate();
        $this->authorize('update', $candidate);
        $data = $request->validated();
        if ($request->boolean('is_current')) $data['to_date'] = null;
        $candidate->experience()->create($data);
        return back()->with('success', 'Experience record added.');
    }

    public function updateExperience(ExperienceRequest $request, WorkExperience $exp)
    {
        $this->authorize('update', $exp);
        $data = $request->validated();
        if ($request->boolean('is_current')) $data['to_date'] = null;
        $exp->update($data);
        return back()->with('success', 'Experience record updated.');
    }

    public function deleteExperience(WorkExperience $exp)
    {
        $this->authorize('delete', $exp);
        $exp->delete();
        return back()->with('success', 'Experience record removed.');
    }
}
