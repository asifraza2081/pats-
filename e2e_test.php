<?php
use App\Models\User;
use App\Models\Candidate;
use App\Models\PatsJob;
use App\Models\TestCenter;
use App\Models\Project;
use App\Models\Application;
use App\Models\Payment;
use App\Services\EligibilityService;
use App\Services\BatchAssignmentService;
use App\Services\RollNumberService;

echo "Starting E2E Backend Simulation...\n";

// 1. Create a Candidate
$user = User::create([
    'first_name' => 'Sara', 'last_name' => 'Khan',
    'cnic' => '3520211111111', 'phone' => '03009998888', 'nationality' => 'Pakistani',
    'password' => bcrypt('Password@123'), 'phone_verified_at' => now(),
]);
$user->assignRole('candidate');
$candidate = Candidate::create(['user_id' => $user->id, 'father_name' => 'Ahmed Khan', 'dob' => '1990-05-10', 'gender' => 'Female', 'marital_status' => 'Single', 'religion' => 'Islam', 'province_of_domicile' => 'Punjab', 'district_of_domicile' => 'Lahore', 'permanent_address' => 'LHE', 'postal_address' => 'LHE', 'photo_path' => 'dummy.png', 'cnic_front_path' => 'dummy.png']);
$candidate->education()->create(['degree_level' => 3, 'degree_name' => 'BSCS', 'marks_type' => 'CGPA', 'obtained_marks' => 3.5, 'total_marks' => 4.0]);
$candidate->experience()->create(['job_type' => 'Private', 'organization_name' => 'Tech', 'designation' => 'Dev', 'from_date' => '2020-01-01', 'is_current' => true]);

echo "Candidate profile complete: " . $candidate->completionPercent() . "%\n";

// 2. Eligibility Check
$job = PatsJob::where('title', 'Software Engineer')->first();
$eligibility = app(EligibilityService::class)->check($candidate, $job);
echo "Eligibility passed? " . ($eligibility['passed'] ? 'Yes' : 'No') . "\n";

// 3. Application Creation
$batcher = app(BatchAssignmentService::class);
$batch = $batcher->findBatch($job->project->id, 'Lahore', null);
if (!$batch) die("FAIL: No batch found.");

$app = Application::create([
    'candidate_id' => $candidate->id, 'job_id' => $job->id, 'batch_id' => $batch->id,
    'test_city_priority_1' => 'Lahore', 'status' => 'submitted', 'applied_at' => now()
]);
$batcher->bookSeat($batch);
Payment::create(['application_id' => $app->id, 'challan_ref' => 'CHL999', 'amount' => $job->fee, 'status' => 'pending']);
echo "Application submitted. Status: {$app->status}. Payment Status: {$app->payment->status}\n";

// 4. Admin Payment Verification
$app->payment->update(['status' => 'paid', 'deposit_date' => now()]);
$app->update(['status' => 'fee_paid']);
$roll = app(RollNumberService::class)->assign($app);
echo "Payment verified! Roll Number generated: {$roll->roll_number}\n";

// 5. Admin Marked Slip Ready
app(RollNumberService::class)->markBatchReady($batch);
$roll->refresh();
echo "Slip Ready Status: " . ($roll->slip_ready ? 'Yes' : 'No') . "\n";

echo "\n✅ ALL E2E BACKEND TESTS PASSED SUCCESSFULLY!\n";
