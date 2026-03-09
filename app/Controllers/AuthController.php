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
use App\Models\User;
use App\Services\SmsService;

class AuthController
{
    // ── Register ────────────────────────────────────────────

    public function showRegister(Request $request, array $params = []): void
    {
        Auth::requireGuest();
        View::render('auth/register', ['pageTitle' => 'Register']);
    }

    public function register(Request $request, array $params = []): void
    {
        Auth::requireGuest();
        CSRF::check();

        $data = $request->only('cnic', 'name', 'email', 'phone', 'password', 'password_confirmation');

        $v = Validator::make($data, [
            'cnic'                  => 'required|cnic',
            'name'                  => 'required|min:3|max:120',
            'email'                 => 'required|email',
            'phone'                 => 'required|phone',
            'password'              => 'required|min:8',
            'password_confirmation' => 'required|confirmed:password',
        ]);

        // Bail early on format errors
        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('old', $data);
            Response::redirect('/register');
        }

        // Check uniqueness
        $cnic = preg_replace('/[-\s]/', '', $data['cnic'] ?? '');
        if (User::findByCnic($cnic)) {
            Session::flash('error', 'A user with this CNIC already exists.');
            Session::flash('old', $data);
            Response::redirect('/register');
        }
        if (User::findByEmail(strtolower($data['email'] ?? ''))) {
            Session::flash('error', 'This email address is already registered.');
            Session::flash('old', $data);
            Response::redirect('/register');
        }

        // Create user + candidate profile
        $userId = User::register($data);
        Candidate::createForUser($userId);

        // Generate & send OTP
        $otp = (string) random_int(100000, 999999);
        User::setOtp($userId, $otp);

        $sms = new SmsService();
        $sms->send($data['phone'], "PATS: Your verification OTP is {$otp}. Expires in 10 minutes.", 'registration_otp');

        Session::set('otp_user_id', $userId);
        Session::flash('success', 'Registration successful! Please enter the OTP sent to your mobile number.');
        Response::redirect('/verify-otp');
    }

    // ── OTP Verification ─────────────────────────────────────

    public function verifyOtp(Request $request, array $params = []): void
    {
        if ($request->isPost()) {
            CSRF::check();
            $userId = (int) Session::get('otp_user_id');
            $otp    = trim($request->post('otp', ''));

            if (!$userId) {
                Session::flash('error', 'Session expired. Please register again.');
                Response::redirect('/register');
            }

            if (User::verifyOtp($userId, $otp)) {
                User::markVerified($userId);
                Session::remove('otp_user_id');
                Session::flash('success', 'Phone verified! Please login to continue.');
                Response::redirect('/login');
            }

            Session::flash('error', 'Invalid or expired OTP. Please try again.');
            Response::redirect('/verify-otp');
        }

        View::render('auth/verify_otp', ['pageTitle' => 'Verify OTP']);
    }

    // ── Login ────────────────────────────────────────────────

    public function showLogin(Request $request, array $params = []): void
    {
        Auth::requireGuest();
        View::render('auth/login', ['pageTitle' => 'Login']);
    }

    public function login(Request $request, array $params = []): void
    {
        Auth::requireGuest();
        CSRF::check();

        $cnic     = trim($request->post('cnic', ''));
        $password = $request->post('password', '');

        $user = User::attempt($cnic, $password);

        if (!$user) {
            Session::flash('error', 'Invalid CNIC or password.');
            Session::flash('old', ['cnic' => $cnic]);
            Response::redirect('/login');
        }

        Auth::login($user);

        // Redirect based on role
        if (Auth::isAdmin()) {
            Response::redirect('/admin/dashboard');
        }

        Response::redirect('/dashboard');
    }

    // ── Logout ───────────────────────────────────────────────

    public function logout(Request $request, array $params = []): void
    {
        Auth::logout();
        Session::flash('success', 'You have been logged out.');
        Response::redirect('/login');
    }

    // ── Forgot Password ──────────────────────────────────────

    public function showForgotPassword(Request $request, array $params = []): void
    {
        View::render('auth/forgot_password', ['pageTitle' => 'Forgot Password']);
    }

    public function forgotPassword(Request $request, array $params = []): void
    {
        CSRF::check();

        $cnic = preg_replace('/[-\s]/', '', trim($request->post('cnic', '')));
        $user = User::findByCnic($cnic);

        if (!$user) {
            Session::flash('error', 'No account found with this CNIC.');
            Response::redirect('/forgot-password');
        }

        $otp = (string) random_int(100000, 999999);
        User::setOtp((int) $user['id'], $otp);

        $sms = new SmsService();
        $sms->send($user['phone'], "PATS: Your password reset OTP is {$otp}. Expires in 10 minutes.", 'password_reset');

        Session::set('reset_user_id', $user['id']);
        Session::flash('success', 'OTP sent to your registered mobile number.');
        Response::redirect('/verify-otp');
    }
}
