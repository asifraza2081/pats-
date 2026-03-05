<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\Candidate;
use App\Models\EducationHistory;
use App\Services\FileUploadService;

class CandidateController
{
    public function dashboard(Request $request, array $params = []): void
    {
        Auth::requireAuth();
        $userId = Auth::id();

        // Redirect admin away from candidate dashboard
        if (Auth::isAdmin()) {
            Response::redirect('/admin/dashboard');
        }

        $profile = Candidate::getFullProfile(Auth::id());
        $completion = Candidate::completionPercent($profile);

        View::render('candidate/dashboard', [
            'pageTitle'  => 'Candidate Dashboard',
            'profile'    => $profile,
            'completion' => $completion
        ]);
    }

    public function showProfile(Request $request, array $params = []): void
    {
        Auth::requireAuth();
        Auth::requireRole('candidate');

        $profile = Candidate::getFullProfile(Auth::id());
        $education = EducationHistory::getForCandidate((int)$profile['id']);

        View::render('candidate/profile', [
            'pageTitle' => 'My Profile',
            'profile'   => $profile,
            'education' => $education
        ]);
    }

    public function updateProfile(Request $request, array $params = []): void
    {
        Auth::requireAuth();
        CSRF::check();

        $profile = Candidate::getFullProfile(Auth::id());
        
        // Cannot edit if locked by an active application submission
        if ($profile['profile_locked']) {
            Session::flash('error', 'Your profile is locked because you have submitted applications.');
            Response::redirect('/profile');
        }

        $data = $request->only('dob', 'gender', 'religion', 'domicile', 'province', 'address');

        // Handle Photo Upload
        $photoFile = $request->file('photo');
        if ($photoFile) {
            try {
                $uploader = new FileUploadService('photos');
                $photoPath = $uploader->upload($photoFile, ['image/jpeg', 'image/png', 'image/webp'], 2 * 1024 * 1024);
                if ($photoPath) $data['photo_path'] = $photoPath;
            } catch (\Exception $e) {
                Session::flash('error', $e->getMessage());
                Response::redirect('/profile');
            }
        }

        // Handle CNIC Copy Upload
        $cnicFile = $request->file('cnic_copy');
        if ($cnicFile) {
            try {
                $uploader = new FileUploadService('docs');
                $cnicPath = $uploader->upload($cnicFile, ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'], 5 * 1024 * 1024);
                if ($cnicPath) $data['cnic_copy_path'] = $cnicPath;
            } catch (\Exception $e) {
                Session::flash('error', $e->getMessage());
                Response::redirect('/profile');
            }
        }

        Candidate::updateWhere($data, ['id' => $profile['id']]);

        // Submitting Education History (simple batch replace for this demo)
        $degrees      = $request->post('degrees', []);
        $institutions = $request->post('institutions', []);
        $years        = $request->post('years', []);
        $grades       = $request->post('grades', []);

        if (!empty($degrees)) {
            \App\Core\Database::getInstance()->beginTransaction();
            try {
                // Clear old
                EducationHistory::deleteWhere(['candidate_id' => $profile['id']]);
                
                // Insert new
                foreach ($degrees as $index => $deg) {
                    if (trim($deg) !== '') {
                        EducationHistory::create([
                            'candidate_id' => $profile['id'],
                            'degree'       => $deg,
                            'institution'  => $institutions[$index] ?? '',
                            'passing_year' => $years[$index] ?: null,
                            'grade'        => $grades[$index] ?? '',
                        ]);
                    }
                }
                \App\Core\Database::getInstance()->commit();
            } catch (\Exception $e) {
                \App\Core\Database::getInstance()->rollBack();
            }
        }

        Session::flash('success', 'Profile updated successfully.');
        Response::redirect('/profile');
    }
}
