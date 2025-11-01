<?php

namespace App\Http\Controllers\Profile;

use App\Actions\User\DeactivateUserAccount;
use App\Actions\User\DeleteUserAccount;
use App\Actions\User\UpdateUserProfile;
use App\Actions\User\ValidateUsernameConfirmation;
use App\Http\Controllers\Controller;
use App\Models\User\Member;
use App\Rules\MatchesUsername;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Throwable;

class UserProfileController extends Controller
{
    public function __construct(
        private UpdateUserProfile $updateUserProfile,
        private DeactivateUserAccount $deactivateUserAccount,
        private DeleteUserAccount $deleteUserAccount,
        private ValidateUsernameConfirmation $validateUsernameConfirmation
    ) {}

    public function show(?Member $user = null)
    {
        if (! $user || Auth::id() === $user->member_id) {
            $user = Auth::user();
        }

        $user->load(['staff.role', 'account.data']);

        return view('pages.sidebar.profiles.profile.users', compact('user'));
    }

    public function update(Request $request, ?Member $user = null)
    {
        $target = $user && Auth::id() !== $user->member_id ? $user->load('account.data', 'staff') : Auth::user()->load('account.data', 'staff');

        if ($request->input('action') === 'change_password') {
            $request->validate([
                'username_confirmation_change' => [
                    'required',
                    'string',
                    new MatchesUsername($target, $this->validateUsernameConfirmation),
                ],
                'new_password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            ]);

            $this->updateUserProfile->changePassword($target, $request->new_password);

            return back()->with('success', 'The user password has been updated.');
        }

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z ]+$/',
                Rule::unique('members')->where(
                    fn ($q) => $q->where('member_type', 'Staff')
                        ->where('first_name', $request->first_name)
                        ->where('middle_name', $request->middle_name)
                        ->where('last_name', $request->last_name)
                        ->where('suffix', $request->suffix)
                        ->where('member_id', '!=', $target->member_id)
                ),
            ],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z ]*$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z ]+$/'],
            'suffix' => ['nullable', 'string', Rule::in(['Sr.', 'Jr.', 'II', 'III', 'IV', 'V'])],
            'profile_picture' => ['nullable', 'image', 'max:8192', 'mimes:jpg,jpeg,jfif,png,webp'],
            'remove_profile_picture_flag' => ['boolean'],
        ], [
            'first_name.unique' => 'This user account already exists with the same name.',
        ]);

        $this->updateUserProfile->execute($target, $validated);

        return back()->with('success', 'User profile has been updated.');
    }

    public function deactivate(Request $request, Member $user)
    {
        $auth = Auth::user();
        $target = $user->member_id !== $auth->member_id ? $user : $auth;

        $request->validate([
            'username_confirmation_deactivate' => [
                'required',
                'string',
                new MatchesUsername($target, $this->validateUsernameConfirmation),
            ],
        ]);

        $wasActive = $target->account->account_status === 'Active';

        $this->deactivateUserAccount->execute($target);

        if ($auth->member_id === $target->member_id && $wasActive) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('success', 'Your account has been deactivated successfully.');
        }

        return redirect()->route('profiles.users.list')
            ->with('success', 'User account has been '.($wasActive ? 'deactivated' : 'activated').' successfully.');
    }

    public function destroy(Request $request, Member $user)
    {
        $auth = Auth::user();
        $target = $user->member_id !== $auth->member_id ? $user : $auth;

        $request->validate([
            'username_confirmation_delete' => [
                'required',
                'string',
                new MatchesUsername($target, $this->validateUsernameConfirmation),
            ],
        ]);

        try {
            $this->deleteUserAccount->execute($target);

            if ($auth->member_id === $target->member_id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->with('success', 'Your account has been deleted successfully.');
            }

            return redirect()->route('profiles.users.list')
                ->with('success', 'User account has been deleted successfully.');
        } catch (Throwable $e) {
            return back()->with('error', 'Failed to delete user account: '.$e->getMessage());
        }
    }
}
