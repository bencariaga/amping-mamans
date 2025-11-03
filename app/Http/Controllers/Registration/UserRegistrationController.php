<?php

namespace App\Http\Controllers\Registration;

use App\Actions\Role\GetRolesWithData;
use App\Actions\User\CheckUserDuplication;
use App\Actions\User\RegisterUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class UserRegistrationController extends Controller
{
    public function __construct(
        private RegisterUser $registerUser,
        private GetRolesWithData $getRolesWithData,
        private CheckUserDuplication $checkUserDuplication
    ) {}

    public function create()
    {
        $roles = $this->getRolesWithData->execute();

        return view('pages.sidebar.profiles.register.user', ['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z ]+$/'],
            'middle_name' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z ]*$/'],
            'last_name' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z ]+$/'],
            'suffix' => ['nullable', Rule::in(['Sr.', 'Jr.', 'II', 'III', 'IV', 'V'])],
            'role_id' => ['nullable', 'string', 'exists:roles,role_id', 'required_without:custom_role'],
            'custom_role' => ['nullable', 'string', 'max:20', 'required_without:role_id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'max:8192', 'mimes:jpg,jpeg,jfif,png,webp'],
        ]);

        if ($this->checkUserDuplication->execute($validated)) {
            return back()->withInput()->withErrors([
                'duplicate_user' => 'This user account already exists with the same name.',
            ]);
        }

        try {
            $this->registerUser->execute($validated);

            return redirect()->route('profiles.users.list')
                ->with('success', 'User account has been added successfully.');
        } catch (Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => 'Registration failed: '.$e->getMessage(),
            ]);
        }
    }
}
