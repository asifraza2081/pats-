<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Requests\Candidate\StepBioRequest;
use App\Http\Requests\Candidate\StepDocsRequest;
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

    public function show(Request $request)
    {
        $candidate = $this->candidate();
        $step = (int) $request->get('step', $candidate->wizard_step ?? 1);
        
        // Sequential Blocking: Ensure they can't jump ahead
        if (!$candidate->isStepAccessible($step)) {
            $status = app(\App\Services\EligibilityService::class)->getProfileStatus($candidate);
            return redirect()->route('candidate.profile.show', ['step' => $status['next_step']])
                ->with('error', 'Please complete previous steps first.');
        }

        // Update the candidate's current step if it's a forward movement
        if ($step > ($candidate->wizard_step ?? 1)) {
            $candidate->update(['wizard_step' => $step]);
        }

        $education = $candidate->education()->orderBy('degree_level', 'desc')->get();
        $experience = $candidate->experience()->orderBy('from_date', 'desc')->get();
        $cities = City::orderBy('name')->get();
        
        return view('candidate.profile', compact('candidate', 'education', 'experience', 'cities', 'step'));
    }

    public function viewProfile()
    {
        $candidate = $this->candidate()->load(['education', 'experience', 'user', 'domicileCity', 'addressCity']);
        return view('candidate.profile-bio', compact('candidate'));
    }

    public function updateBio(StepBioRequest $request)
    {
        $candidate = $this->candidate();
        $this->authorize('update', $candidate);

        $data = $request->validated();

        // Handle same postal address
        if ($data['same_postal_address']) {
            $data['postal_address'] = $data['permanent_address'];
        }

        // Capture CNIC before unsetting it
        $cnicFromForm = $data['cnic'] ?? null;
        unset($data['cnic']);

        // Prevent modification of critical identity/eligibility data if profile is locked
        if ($candidate->profile_locked) {
            unset(
                $data['dob'], 
                $data['father_name'], 
                $data['gender'], 
                $data['religion'],
                $data['domicile_city_id'], 
                $data['province_of_domicile'], 
                $data['district_of_domicile']
            );
        }

        $candidate->update($data);

        // Update core user fields if not locked
        if (!$candidate->profile_locked && $cnicFromForm) {
            $user = auth()->user();
            $user->update(['cnic' => $cnicFromForm]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Biographical information saved.',
                'next_step' => 2
            ]);
        }

        return redirect()->route('candidate.profile.show', ['step' => 2])
            ->with('success', 'Biographical information saved. Proceed to Step 2.');
    }

    public function updateDocs(StepDocsRequest $request)
    {
        $candidate = $this->candidate();
        $this->authorize('update', $candidate);

        $data = $request->validated();

        // Photo upload handling
        if ($request->hasFile('photo')) {
            if ($candidate->photo_path) Storage::delete($candidate->photo_path);
            $candidate->photo_path = $request->file('photo')->store('photos', 'public');
        }

        // CNIC Front upload handling
        if ($request->hasFile('cnic_copy')) {
            if ($candidate->cnic_front_path) Storage::delete($candidate->cnic_front_path);
            $candidate->cnic_front_path = $request->file('cnic_copy')->store('cnics', 'public');
        }

        $candidate->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Documents uploaded successfully.'
            ]);
        }

        return redirect()->route('candidate.profile.show', ['step' => 4])
            ->with('success', 'Documents uploaded successfully.');
    }

    // ── Education ─────────────────────────────────────────────
    public function addEducation(EducationRequest $request)
    {
        try {
            $candidate = $this->candidate();
            $this->authorize('update', $candidate);
            $data = $request->validated();
            $edu = $candidate->education()->create($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Education record added.',
                    'html' => view('candidate.partials._education_row', ['edu' => $edu, 'candidate' => $candidate])->render()
                ]);
            }

            return back()->with('success', 'Education record added.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            throw $e;
        }
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
        $exp = $candidate->experience()->create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Experience record added.',
                'html' => view('candidate.partials._experience_row', ['exp' => $exp, 'candidate' => $candidate])->render()
            ]);
        }

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
