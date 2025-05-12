<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Assuming "User" model is used for both parents and pediatricians
use App\Models\Pediatrician;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login request
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    $credentials = $request->only('email', 'password');

    Log::info('Login attempt', $credentials);

    if (Auth::validate($credentials)) {
        $user = User::where('email', $credentials['email'])->first();

        // التحقق من حالة القبول فقط إذا كان طبيب أطفال
        if ($user->is_pediatrician) {
            $pediatrician = Pediatrician::find($user->id);

            if ($pediatrician && !$pediatrician->accepted) {
                return back()->withErrors(['email' => 'Votre compte n\'a pas encore été accepté. Veuillez patienter.']);
            }
        }

        // تسجيل الدخول الفعلي
        if (Auth::attempt($credentials)) {
            return redirect()->route('welcome');
        }
    }

    Log::error('Failed login attempt for email: ' . $credentials['email']);
    return back()->withErrors(['email' => 'Identifiants invalides.']);
}


    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate the session and regenerate the token for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }
}
