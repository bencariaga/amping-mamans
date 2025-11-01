<?php

namespace App\Actions\User;

use App\Models\User\Member;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ValidateUserProfileUpdate
{
    public function execute(Request $request, Member $target): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z ]+$/', Rule::unique('members')->where(fn ($q) => $q->where('member_type', 'Staff')->where('first_name', $request->first_name)->where('middle_name', $request->middle_name)->where('last_name', $request->last_name)->where('suffix', $request->suffix)->where('member_id', '!=', $target->member_id))],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z ]*$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z ]+$/'],
            'suffix' => ['nullable', 'string', Rule::in(['Sr.', 'Jr.', 'II', 'III', 'IV', 'V'])],
            'profile_picture' => ['nullable', 'image', 'max:8192', 'mimes:jpg,jpeg,jfif,png,webp'],
            'remove_profile_picture_flag' => ['boolean'],
        ], [
            'first_name.unique' => 'This user account already exists with the same name.',
        ]);
    }
}
