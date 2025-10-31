<?php

namespace App\Actions\User;

use App\Models\User\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UpdateUserProfile
{
    public function execute(Member $user, array $validatedData): Member
    {
        $fullName = collect([
            $validatedData['first_name'],
            $validatedData['middle_name'] ?? null,
            $validatedData['last_name'],
            $validatedData['suffix'] ?? null,
        ])->filter()->implode(' ');

        $user->fill($validatedData);
        $user->save();

        $staff = $user->staff;

        if (isset($validatedData['remove_profile_picture_flag']) && $validatedData['remove_profile_picture_flag']) {
            if ($staff->file_name) {
                Storage::disk('public')->delete($staff->file_name);
                $staff->file_name = null;
                $staff->file_extension = null;
                $staff->save();
            }
        } elseif (isset($validatedData['profile_picture'])) {
            if ($staff->file_name) {
                Storage::disk('public')->delete($staff->file_name);
            }

            $file = $validatedData['profile_picture'];
            $path = $file->store('profile_pictures', 'public');
            $ext = $file->getClientOriginalExtension();

            $staff->file_name = $path;
            $staff->file_extension = '.' . $ext;
            $staff->save();
        }

        return $user->fresh(['staff.role', 'account.data']);
    }

    public function changePassword(Member $user, string $newPassword): void
    {
        $staff = $user->staff;
        $staff->password = Hash::make($newPassword);
        $staff->save();
    }
}
