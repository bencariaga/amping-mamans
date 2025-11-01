<?php

namespace App\Actions\User;

use App\Models\Operation\Data;
use App\Models\User\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteUserAccount
{
    public function execute(Member $user): void
    {
        DB::transaction(function () use ($user) {
            $mainDataId = $user->account->data_id;
            $staff = $user->staff;

            if ($staff && $staff->file_name) {
                Storage::disk('public')->delete($staff->file_name);
            }

            $user->staff()->delete();
            $user->delete();
            $user->account()->delete();

            Data::where('data_id', $mainDataId)->delete();
        });
    }
}
