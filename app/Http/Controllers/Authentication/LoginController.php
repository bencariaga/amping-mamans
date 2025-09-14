<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User\Member;

class LoginController extends Controller
{
    public function showLogin(): View
    {
        return view('pages.authentication.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = Member::with('staff')
            ->whereRaw("CONCAT(first_name, ' ', last_name) = ?", [$credentials['username']])
            ->first();

        if ($user && $user->staff && Hash::check($credentials['password'], $user->staff->password)) {
            Auth::login($user);
            return redirect()->route('dashboard');
        }

        return redirect()
            ->back()
            ->withInput(['username' => $credentials['username']])
            ->withErrors(['username' => 'Invalid username / password, please try again.']);
    }
}
