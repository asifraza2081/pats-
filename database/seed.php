<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

// Load environment manually since we are running CLI
define('BASE_PATH', realpath(__DIR__ . '/../') . '/');

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();


$db = Database::getInstance();

echo "Starting Database Seeding...\n";

// Ensure tables are clear for idempotency (optional, but good for testing)
// We will just truncate the tables we are seeding
$tables = ['payments', 'applications', 'center_slots', 'project_centers', 'test_centers', 'jobs', 'projects', 'education_history', 'candidates', 'users'];
$db->getPdo()->exec("SET FOREIGN_KEY_CHECKS=0;");
foreach ($tables as $table) {
    $db->getPdo()->exec("TRUNCATE TABLE {$table};");
}
$db->getPdo()->exec("SET FOREIGN_KEY_CHECKS=1;");

$passHash = password_hash('password123', PASSWORD_BCRYPT);

// 1. Create Users
echo "Seeding Users...\n";
$users = [
    ['cnic' => '1111111111111', 'email' => 'admin@pats.test', 'password' => $passHash, 'phone' => '03001111111', 'name' => 'System Admin', 'role' => 'super_admin', 'is_verified' => 1],
    ['cnic' => '2222222222222', 'email' => 'data@pats.test', 'password' => $passHash, 'phone' => '03002222222', 'name' => 'Data Entry Officer', 'role' => 'data_entry', 'is_verified' => 1],
    ['cnic' => '3520212345671', 'email' => 'ali@pats.test', 'password' => $passHash, 'phone' => '03001234567', 'name' => 'Ali Khan', 'role' => 'candidate', 'is_verified' => 1],
    ['cnic' => '3520276543212', 'email' => 'fatima@pats.test', 'password' => $passHash, 'phone' => '03007654321', 'name' => 'Fatima Ahmed', 'role' => 'candidate', 'is_verified' => 1],
];

$userIds = [];
foreach ($users as $u) {
    try {
        $userIds[$u['email']] = $db->insert('users', $u);
    } catch (Exception $e) {
        echo "Error inserting user {$u['email']}: " . $e->getMessage() . "\n";
    }
}

// 2. Create Candidate Profiles
echo "Seeding Candidates...\n";
$aliId = $userIds['ali@pats.test'];
$fatimaId = $userIds['fatima@pats.test'];

$aliCandidateId = $db->insert('candidates', [
    'user_id' => $aliId, 'gender' => 'male', 'dob' => '1995-05-10', 
    'province' => 'Punjab', 'domicile' => 'Lahore', 
    'address' => '123 Main St, Lahore', 'profile_locked' => 0
]);

$fatimaCandidateId = $db->insert('candidates', [
    'user_id' => $fatimaId, 'gender' => 'female', 'dob' => '1998-08-20', 
    'province' => 'Sindh', 'domicile' => 'Karachi', 
    'address' => '456 Defense, Karachi', 'profile_locked' => 0
]);

// Add Education
$db->insert('education_history', ['candidate_id' => $aliCandidateId, 'degree' => 'BSCS', 'subject' => 'Computer Science', 'institution' => 'PU Lahore', 'passing_year' => 2018, 'grade' => '3.5 CGPA']);
$db->insert('education_history', ['candidate_id' => $fatimaCandidateId, 'degree' => 'MBA', 'subject' => 'Finance', 'institution' => 'IBA Karachi', 'passing_year' => 2022, 'grade' => '1st Division']);

// 3. Create Projects
echo "Seeding Projects...\n";
$fbrProjectId = $db->insert('projects', [
    'name' => 'Federal Board of Revenue (FBR) Recruitment 2026', 'org_name' => 'FBR',
    'description' => 'Recruitment for various posts in FBR across Pakistan. Eligible candidates must apply online.',
    'open_date' => date('Y-m-d', strtotime('-5 days')), 'close_date' => date('Y-m-d', strtotime('+15 days')), 
    'test_date' => date('Y-m-d', strtotime('+30 days')), 'status' => 'open', 'created_by' => $userIds['admin@pats.test']
]);

$db->insert('projects', [
    'name' => 'WAPDA Engineering Jobs 2026', 'org_name' => 'WAPDA',
    'description' => 'Draft project for ongoing WAPDA recruitment.',
    'open_date' => date('Y-m-d', strtotime('+10 days')), 'close_date' => date('Y-m-d', strtotime('+25 days')), 
    'status' => 'draft', 'created_by' => $userIds['admin@pats.test']
]);

// 4. Create Jobs for FBR
echo "Seeding Jobs...\n";
$jobs = [
    ['project_id' => $fbrProjectId, 'title' => 'Inspector Inland Revenue', 'department' => 'Inland Revenue', 'bps_grade' => 'BPS-16', 'total_seats' => 50, 'min_qualification' => 'Bachelor', 'age_min' => 20, 'age_max' => 28, 'domicile_required' => 'Open Merit', 'fee' => 500],
    ['project_id' => $fbrProjectId, 'title' => 'Upper Division Clerk (UDC)', 'department' => 'Administration', 'bps_grade' => 'BPS-11', 'total_seats' => 120, 'min_qualification' => 'Intermediate', 'age_min' => 18, 'age_max' => 25, 'domicile_required' => 'Punjab', 'fee' => 300],
    ['project_id' => $fbrProjectId, 'title' => 'Lower Division Clerk (LDC)', 'department' => 'Administration', 'bps_grade' => 'BPS-09', 'total_seats' => 200, 'min_qualification' => 'Matric', 'age_min' => 18, 'age_max' => 25, 'domicile_required' => 'Sindh', 'fee' => 300],
];

$jobIds = [];
foreach ($jobs as $j) {
    $jobIds[] = $db->insert('jobs', $j);
}

// 5. Create Test Centers
echo "Seeding Test Centers & Slots...\n";
$centers = [
    ['name' => 'Govt Science College', 'city' => 'Lahore', 'province' => 'Punjab', 'address' => 'Wahdat Road, Lahore', 'is_active' => 1],
    ['name' => 'Karachi University Expo', 'city' => 'Karachi', 'province' => 'Sindh', 'address' => 'Main University Road', 'is_active' => 1],
    ['name' => 'Islamabad Model College', 'city' => 'Islamabad', 'province' => 'Federal', 'address' => 'F-8/4, Islamabad', 'is_active' => 1],
];

$centerIds = [];
foreach ($centers as $c) {
    $centerIds[] = $db->insert('test_centers', $c);
}

// Map Centers to Project and add Slots
$fbrTestDate = date('Y-m-d', strtotime('+30 days'));

$db->insert('project_centers', ['project_id' => $fbrProjectId, 'center_id' => $centerIds[0]]); // Lahore
$lahoreSlotId = $db->insert('center_slots', ['center_id' => $centerIds[0], 'project_id' => $fbrProjectId, 'slot_date' => $fbrTestDate, 'slot_time' => '09:00:00', 'total_seats' => 100, 'booked_seats' => 0]);

$db->insert('project_centers', ['project_id' => $fbrProjectId, 'center_id' => $centerIds[1]]); // Karachi
$karachiSlotId = $db->insert('center_slots', ['center_id' => $centerIds[1], 'project_id' => $fbrProjectId, 'slot_date' => $fbrTestDate, 'slot_time' => '14:00:00', 'total_seats' => 50, 'booked_seats' => 0]);

echo "Seeding Complete!\n";

echo "\n--- DUMMY ACCOUNTS ---\n";
echo "Admin (System): CNIC: 1111111111111 / pass: password123\n";
echo "Admin (Data Entry): CNIC: 2222222222222 / pass: password123\n";
echo "Candidate 1 (Ali): CNIC: 3520212345671 / pass: password123\n";
echo "Candidate 2 (Fatima): CNIC: 3520276543212 / pass: password123\n";
