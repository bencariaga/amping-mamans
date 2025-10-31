<?php

namespace App\Http\Controllers\Registration;

use App\Actions\User\RegisterUser;
use App\Http\Controllers\Controller;
use App\Models\Authentication\Role;
use App\Models\User\Member;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class UserRegistrationController extends Controller
{
    public function __construct(
        private RegisterUser $registerUser
    ) {}

    public function create()
    {
        $roles = Role::join('data', 'roles.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get();

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

        $existsUser = Member::where('member_type', 'Staff')
            ->where('first_name', $validated['first_name'])
            ->where('middle_name', $validated['middle_name'] ?? null)
            ->where('last_name', $validated['last_name'])
            ->exists();

        if ($existsUser) {
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
                'error' => 'Registration failed: ' . $e->getMessage()
            ]);
        }
    }
}
