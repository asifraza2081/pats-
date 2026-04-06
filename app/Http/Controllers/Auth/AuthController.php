<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function __construct(private SmsService $sms) {}

    // ── Registration ──────────────────────────────────────────
    public function showRegister() 
    { 
        return view('auth.register'); 
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name'  => 'required|string|max:80',
            'last_name'   => 'required|string|max:80',
            'email'       => 'required|email|unique:users',
            'cnic'        => ['nullable', 'regex:/^\d{5}-\d{7}-\d{1}$/', 'unique:users'],
            'phone'       => 'required|string|max:15',
            'captcha'     => 'required|captcha',
            'password'    => ['required', 'confirmed', PasswordRule::min(8)],
        ], [
            'cnic.regex' => 'CNIC must be in the format XXXXX-XXXXXXX-X.',
            'captcha.captcha' => 'The CAPTCHA code is incorrect.',
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'cnic'       => $data['cnic'],
            'phone'      => $data['phone'],
            'password'   => Hash::make($data['password']),
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
        $request->validate(['identifier' => ['required', 'string']]);
        
        $user = User::where('cnic', $request->identifier)
            ->orWhere('email', $request->identifier)
            ->first();

        if (!$user || !$user->email) {
            return back()->withErrors(['identifier' => 'No account found with an associated email address.']);
        }

        // Standard Laravel Token-based Reset
        $status = PasswordBroker::broker()->sendResetLink(['email' => $user->email]);

        return $status === PasswordBroker::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['identifier' => __($status)]);
    }

    public function showResetPassword(Request $request, ?string $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = PasswordBroker::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === PasswordBroker::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
