<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\HrEmployee;
use App\Models\CompanySetting;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            if (Auth::user()->isEmployee()) {
                return redirect()->route('employee.dashboard');
            }
            return redirect()->route('dashboard');
        }

        $company = CompanySetting::first() ?? new CompanySetting([
            'company_name' => 'Turkey Bangla Garments Ltd',
            'phone' => '+880 1712-345678'
        ]);

        $defaultPortal = $request->query('portal', 'admin');

        return view('auth.login', compact('company', 'defaultPortal'));
    }

    /**
     * Handle login submission
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login_identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'login_identifier.required' => 'ইমেইল বা কর্মচারী কোড লিখুন (Enter Email or Employee ID)',
            'password.required' => 'পাসওয়ার্ড প্রদান করুন (Enter Password)',
        ]);

        $identifier = trim($request->login_identifier);
        $password = $request->password;
        $remember = $request->boolean('remember');

        // 1. Try finding user directly by email
        $user = User::where('email', $identifier)->first();

        // 2. If not found by email, check if it's an employee_code (e.g. TBG-EMP-001)
        if (!$user) {
            $employee = HrEmployee::where('employee_code', $identifier)
                ->orWhere('employee_code', 'LIKE', '%' . $identifier)
                ->first();

            if ($employee && $employee->user_id) {
                $user = User::find($employee->user_id);
            }
        }

        // 3. Attempt Authentication
        if ($user && Auth::attempt(['email' => $user->email, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            // Redirect based on role
            if (Auth::user()->isEmployee()) {
                return redirect()->intended(route('employee.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'login_identifier' => 'ইউজার আইডি বা পাসওয়ার্ড ভুল হয়েছে। সঠিক তথ্য প্রদান করুন।',
        ])->onlyInput('login_identifier');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'সফলভাবে লগআউট হয়েছে (Logged out successfully).');
    }
}

