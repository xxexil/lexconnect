<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LawyerProfile;
use App\Models\LawFirmProfile;
use App\Models\FirmApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($request->only('email','password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Block unverified users
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Please verify your email address before logging in. Check your inbox for the verification link.'])->onlyInput('email');
            }

            $role = $user->role;
            if ($role === 'lawyer') {
                $profile = $user->lawyerProfile;
                if ($profile) $profile->update(['availability_status' => 'available']);
                return redirect()->route('lawyer.dashboard');
            }
            if ($role === 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                return redirect()->route('admin.login')->withErrors(['email' => 'Please use the admin login portal.']);
            }
            if ($role === 'law_firm') return redirect()->route('lawfirm.dashboard');
            return redirect()->route('dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function showRegister() {
        $lawFirms = LawFirmProfile::select(
            'id','firm_name','tagline','description','address','city',
            'website','phone','founded_year','firm_size','cut_percentage','specialties',
            'rating','reviews_count','is_verified'
        )->get();
        return view('auth.register', compact('lawFirms'));
    }

    public function register(Request $request) {
        $request->validate([
            'name'             => 'required_unless:role,lawyer|nullable|string|max:100',
            'first_name'       => 'required_if:role,lawyer|nullable|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required_if:role,lawyer|nullable|string|max:100',
            'email'            => 'required|email|unique:users',
            'password'         => 'required|min:6|confirmed',
            'role'             => 'required|in:client,lawyer,law_firm',
            'firm_name'        => 'required_if:role,law_firm|nullable|string|max:150',
            'city'             => 'nullable|string|max:100',
            'phone'            => 'nullable|string|max:30',
            'website'          => 'nullable|url|max:200',
            'specialty'        => 'required_if:role,lawyer|nullable|string|max:100',
            'firm'             => 'nullable|string|max:150',
            'hourly_rate'      => 'required_if:role,lawyer|nullable|numeric|min:0',
            'experience_years' => 'required_if:role,lawyer|nullable|integer|min:0',
            'location'         => 'nullable|string|max:150',
            'dti_sec_registration' => 'required_if:role,law_firm|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'business_permit'  => 'required_if:role,law_firm|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'valid_id'         => 'required_if:role,law_firm|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ibp_id'           => 'required_if:role,lawyer|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'firm_ibp_id'      => 'required_if:role,law_firm|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'agreed_terms'     => $request->role === 'lawyer' ? 'required|accepted' : 'nullable',
            'agreed_terms_client_firm' => in_array($request->role, ['client', 'law_firm']) ? 'required|accepted' : 'nullable',
            'government_id'    => 'required_if:role,lawyer|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Build full name for lawyer from split fields
        if ($request->role === 'lawyer') {
            $middle = $request->middle_name ? ' ' . trim($request->middle_name) . ' ' : ' ';
            $fullName = trim($request->first_name . $middle . $request->last_name);
        } else {
            $fullName = $request->name;
        }

        $user = User::create([
            'name'     => $fullName,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        if ($request->role === 'lawyer') {
            $govIdPath = null;
            $ibpIdPath = null;
            if ($request->hasFile('government_id')) {
                $govIdPath = $request->file('government_id')->store('lawyer-docs', 'public');
            }
            if ($request->hasFile('ibp_id')) {
                $ibpIdPath = $request->file('ibp_id')->store('lawyer-docs', 'public');
            }

            LawyerProfile::create([
                'user_id'             => $user->id,
                'specialty'           => $request->specialty,
                'firm'                => $request->firm,
                'hourly_rate'         => $request->hourly_rate ?? 0,
                'experience_years'    => $request->experience_years ?? 0,
                'location'            => $request->location,
                'availability_status' => 'available',
                'is_certified'        => false,
                'rating'              => 0,
                'reviews_count'       => 0,
                'government_id_doc'   => $govIdPath,
                'ibp_id_doc'          => $ibpIdPath,
            ]);

            // If the lawyer chose an existing law firm, auto-create a pending application
            if ($request->filled('firm')) {
                $matchedFirm = LawFirmProfile::where('firm_name', $request->firm)->first();
                if ($matchedFirm) {
                    FirmApplication::create([
                        'lawyer_id'   => $user->id,
                        'law_firm_id' => $matchedFirm->id,
                        'message'     => 'Applied during registration.',
                        'status'      => 'pending',
                    ]);
                }
            }

            Auth::login($user);
            $user->sendEmailVerificationNotification();
            Auth::logout();
            return redirect()->route('verification.notice')->with('registered', true);
        }

        if ($request->role === 'law_firm') {
            $dtiSecPath = $request->hasFile('dti_sec_registration')
                ? $request->file('dti_sec_registration')->store('law-firm-docs', 'public')
                : null;
            $businessPermitPath = $request->hasFile('business_permit')
                ? $request->file('business_permit')->store('law-firm-docs', 'public')
                : null;
            $validIdPath = $request->hasFile('valid_id')
                ? $request->file('valid_id')->store('law-firm-docs', 'public')
                : null;
            $ibpIdPath = $request->hasFile('firm_ibp_id')
                ? $request->file('firm_ibp_id')->store('law-firm-docs', 'public')
                : null;

            LawFirmProfile::create([
                'user_id'   => $user->id,
                'firm_name' => $request->firm_name,
                'city'      => $request->city,
                'phone'     => $request->phone,
                'website'   => $request->website,
                'firm_size' => 'small',
                'cut_percentage' => 5,
                'specialties' => [],
                'is_verified' => false,
                'dti_sec_registration_doc' => $dtiSecPath,
                'business_permit_doc' => $businessPermitPath,
                'valid_id_doc' => $validIdPath,
                'ibp_id_doc' => $ibpIdPath,
                'rating'      => 0,
                'reviews_count' => 0,
            ]);
            Auth::login($user);
            $user->sendEmailVerificationNotification();
            Auth::logout();
            return redirect()->route('verification.notice')->with('registered', true);
        }

        Auth::login($user);
        $user->sendEmailVerificationNotification();
        Auth::logout();
        return redirect()->route('verification.notice')->with('registered', true);
    }

    public function logout(Request $request) {
        // If lawyer, set status to offline before logout
        if (Auth::check() && Auth::user()->role === 'lawyer') {
            $profile = Auth::user()->lawyerProfile;
            if ($profile) {
                $profile->update(['availability_status' => 'offline']);
            }
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
