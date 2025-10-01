<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User\Member;
use App\Models\Authentication\Account;
use Illuminate\Support\Facades\Log;

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
            ->where('member_type', 'Staff')
            ->whereRaw("CONCAT(first_name, ' ', last_name) = ?", [$credentials['username']])
            ->first();
        
        $account = $user ? Account::where('account_id', $user->account_id)->first() : null;
        if ($user && $user->staff && Hash::check($credentials['password'], $user->staff->password) && $account && $account->account_status === 'Active') {
            Auth::login($user);
            return redirect()->route('dashboard');
        }

        return redirect()
            ->back()
            ->withInput(['username' => $credentials['username']])
            ->withErrors(['username' => 'Invalid username / password, please try again.']);
    }
}
