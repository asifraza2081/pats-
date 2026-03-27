<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(private SmsService $sms) {}

    // ── Registration ──────────────────────────────────────────
    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name'  => 'required|string|max:80',
            'last_name'   => 'required|string|max:80',
            'email'       => 'required|email|unique:users',
            'cnic'        => ['required', 'regex:/^\d{13}$/', 'unique:users'],
            'phone'       => 'required|string|max:15',
            'nationality' => 'required|in:Pakistani,Foreigner',
            'password'    => ['required', 'confirmed', Password::min(8)],
        ], [
            'cnic.regex' => 'CNIC must be exactly 13 digits (without dashes).',
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        // Create blank candidate profile
        Candidate::create(['user_id' => $user->id]);

        $user->assignRole('candidate');

        // Auto-verify and Login
        $user->update(['phone_verified_at' => now()]);
        Auth::login($user);

        return redirect()->route('candidate.dashboard')->with('success', 'Registration successful! Welcome to PATS.');
    }

    // ── OTP Verification ─────────────────────────────────────
    public function showOtp() { return view('auth.otp'); }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);
        $userId  = session('otp_user_id');
        $purpose = session('otp_purpose', 'verify_phone');

        if (!$userId) return redirect()->route('login');

        $user = User::findOrFail($userId);

        if (!$user->isOtpValid() || $user->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $user->clearOtp();

        if ($purpose === 'verify_phone') {
            $user->update(['phone_verified_at' => now()]);
            Auth::login($user);
            session()->forget(['otp_user_id', 'otp_purpose']);

            if ($user->hasRole('candidate')) return redirect()->route('candidate.dashboard')->with('success', 'Phone verified!');
            if ($user->hasAnyRole(['admin', 'super_admin'])) return redirect()->route('admin.dashboard');
            if ($user->hasRole('examiner')) return redirect()->route('examiner.dashboard');

            return redirect()->route('home')->with('success', 'Phone verified!');
        }

        if ($purpose === 'reset_password') {
            session(['otp_user_id' => $userId]); // Keep consistent with resetPassword() reading reset_user_id below
            session(['reset_user_id' => $userId]);
            session()->forget(['otp_purpose']);
            return redirect()->route('auth.reset-password');
        }

        return redirect()->route('login');
    }

    public function resendOtp()
    {
        $userId = session('otp_user_id');
        if (!$userId) return redirect()->route('login');

        // Simple Rate Limit: Check last sent time if we added one (future improvement)
        // For now, just ensure the user exists
        $user = User::findOrFail($userId);
        
        // Prevent abuse: only allow resend every 60 seconds (simulated via session)
        if (session('last_otp_resend') && now()->diffInSeconds(session('last_otp_resend')) < 60) {
            return back()->with('error', 'Please wait 60 seconds before requesting another OTP.');
        }

        $otp  = $user->generateOtp();
        $this->sms->send($user->phone, "PATS: Your OTP is {$otp}. Valid for 10 minutes.", $user->id);
        
        session(['last_otp_resend' => now()]);

        return back()->with('success', 'OTP resent to ' . $user->phone);
    }

    // ── Login ─────────────────────────────────────────────────
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $request->validate([
            'cnic'     => 'required|string',
            'password' => 'required',
        ]);

        $loginField = filter_var($request->cnic, FILTER_VALIDATE_EMAIL) ? 'email' : 'cnic';
        $user = User::where($loginField, $request->cnic)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['cnic' => 'Invalid credentials.'])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['cnic' => 'Account suspended. Please contact administrator.'])->withInput();
        }

        if (!$user->phone_verified_at) {
            $user->update(['phone_verified_at' => now()]);
        }

        Auth::login($user, $request->boolean('remember'));

        if ($user->hasAnyRole(['admin', 'super_admin', 'data_entry'])) return redirect()->route('admin.dashboard');
        if ($user->hasRole('examiner')) return redirect()->route('examiner.dashboard');
        if ($user->hasRole('candidate')) return redirect()->route('candidate.dashboard');

        return redirect()->intended('/');
    }

    // ── Logout ────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    // ── Forgot Password ───────────────────────────────────────
    public function showForgotPassword() { return view('auth.forgot-password'); }

    public function forgotPassword(Request $request)
    {
        $request->validate(['cnic' => ['required', 'regex:/^\d{13}$/']]);
        $user = User::where('cnic', $request->cnic)->first();

        if (!$user) return back()->withErrors(['cnic' => 'No account found with this CNIC.']);

        session(['reset_user_id' => $user->id]);
        return redirect()->route('auth.reset-password')->with('info', 'Please set your new password.');
    }

    public function showResetPassword() { return view('auth.reset-password'); }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $userId = session('reset_user_id');
        if (!$userId) return redirect()->route('login');

        User::findOrFail($userId)->update(['password' => Hash::make($request->password)]);
        session()->forget('reset_user_id');

        return redirect()->route('login')->with('success', 'Password reset successfully. Please log in.');
    }
}
