<?php

namespace App\Http\Controllers\Profile;

use App\Actions\User\DeactivateUserAccount;
use App\Actions\User\DeleteUserAccount;
use App\Actions\User\UpdateUserProfile;
use App\Actions\User\ValidateUsernameConfirmation;
use App\Http\Controllers\Controller;
use App\Models\User\Member;
use App\Models\User\Staff;
use App\Rules\MatchesUsername;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class UserProfileController extends Controller
{
    public function __construct(
        private UpdateUserProfile $updateUserProfile,
        private DeactivateUserAccount $deactivateUserAccount,
        private DeleteUserAccount $deleteUserAccount,
        private ValidateUsernameConfirmation $validateUsernameConfirmation
    ) {}

    protected function resolveUser(?string $staffId = null): Member
    {
        $auth = Auth::user();
        $authUserMember = $auth instanceof Member ? $auth : $auth->member;

        if ($staffId === null || ($auth->staff && $staffId === $auth->staff->staff_id)) {
            return $authUserMember;
        }

        $staff = Staff::where('staff_id', $staffId)->first();

        if (! $staff) {
            abort(404, 'User not found.');
        }

        return $staff->member;
    }

    public function show(?string $staffId = null)
    {
        $user = $this->resolveUser($staffId);
        $user->load(['staff.role', 'account.data']);

        return view('pages.sidebar.profiles.profile.users', ['user' => $user]);
    }

    public function update(Request $request, ?string $staffId = null)
    {
        $target = $this->resolveUser($staffId);
        $isSelf = Auth::user()->member_id === $target->member_id;

        if ($request->input('action') === 'change_password') {
            $request->validate([
                'username_confirmation_change' => ['required', 'string', new MatchesUsername($target, $this->validateUsernameConfirmation)],
                'new_password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            ]);

            $this->updateUserProfile->changePassword($target, $request->new_password);

            if ($isSelf) {
                return back()->with('success', 'Your password has been updated successfully.');
            }

            return redirect()->route('profiles.users.list')->with('success', 'User password has been updated successfully.');
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:255'],
        ]);

        $this->updateUserProfile->execute($target, $request->except(['_token', '_method']));

        return redirect()->route('profiles.users.list')->with('success', 'User profile has been updated successfully.');
    }

    public function updateDeactivate(Request $request, ?string $staffId = null)
    {
        $auth = Auth::user();
        $target = $this->resolveUser($staffId);

        $wasActive = $target->account->account_status === 'Active';

        $request->validate(['username_confirmation_deactivate' => ['required', 'string', new MatchesUsername($target, $this->validateUsernameConfirmation)]]);

        $this->deactivateUserAccount->execute($target);

        if ($auth->member_id === $target->member_id && $wasActive) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('success', 'Your account has been deactivated successfully.');
        }

        return redirect()->route('profiles.users.list')->with('success', 'User account has been '.($wasActive ? 'deactivated' : 'activated').' successfully.');
    }

    public function destroy(Request $request, ?string $staffId = null)
    {
        $auth = Auth::user();
        $target = $this->resolveUser($staffId);

        $request->validate(['username_confirmation_delete' => ['required', 'string', new MatchesUsername($target, $this->validateUsernameConfirmation)]]);

        try {
            $this->deleteUserAccount->execute($target);

            if ($auth->member_id === $target->member_id) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->with('success', 'Your account has been deleted successfully.');
            }

            return redirect()->route('profiles.users.list')->with('success', 'User account has been deleted successfully.');
        } catch (Throwable $e) {
            return back()->with('error', 'Failed to delete user account: '.$e->getMessage());
        }
    }
}
