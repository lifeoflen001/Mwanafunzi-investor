<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function create() { return view('admin.auth.login'); }

    public function store(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (Auth::attempt([...$credentials, 'is_admin' => true], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return to_route('admin.dashboard');
        }
        return back()->withErrors(['email' => 'Those admin credentials were not recognised.'])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('admin.login');
    }
}
