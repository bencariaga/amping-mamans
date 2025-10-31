<?php

namespace App\Actions\User;

use App\Actions\DatabaseTableIdGeneration\GenerateAccountId;
use App\Actions\DatabaseTableIdGeneration\GenerateDataId;
use App\Actions\DatabaseTableIdGeneration\GenerateMemberId;
use App\Actions\DatabaseTableIdGeneration\GenerateRoleId;
use App\Actions\DatabaseTableIdGeneration\GenerateStaffId;
use App\Models\Authentication\Account;
use App\Models\Authentication\Role;
use App\Models\Operation\Data;
use App\Models\User\Member;
use App\Models\User\Staff;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUser
{
    public function execute(array $validatedData): Member
    {
        return DB::transaction(function () use ($validatedData) {
            $dataId = GenerateDataId::execute();
            $acctId = GenerateAccountId::execute();
            $memId = GenerateMemberId::execute();
            $staffId = GenerateStaffId::execute();

            $data = Data::create([
                'data_id' => $dataId,
                'archive_status' => 'Unarchived',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $acct = Account::create([
                'account_id' => $acctId,
                'data_id' => $data->data_id,
                'account_status' => 'Active',
            ]);

            if (! empty($validatedData['custom_role'])) {
                $roleDataId = GenerateDataId::execute();
                $roleData = Data::create([
                    'data_id' => $roleDataId,
                    'archive_status' => 'Unarchived',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $roleModel = Role::create([
                    'role_id' => GenerateRoleId::execute(),
                    'data_id' => $roleData->data_id,
                    'role' => $validatedData['custom_role'],
                ]);
            } else {
                $roleModel = Role::findOrFail($validatedData['role_id']);
            }

            $fullName = collect([
                $validatedData['first_name'],
                $validatedData['middle_name'] ?? null,
                $validatedData['last_name'],
                $validatedData['suffix'] ?? null,
            ])->filter()->implode(' ');

            $member = Member::create([
                'member_id' => $memId,
                'account_id' => $acct->account_id,
                'member_type' => 'Staff',
                'first_name' => $validatedData['first_name'],
                'middle_name' => $validatedData['middle_name'] ?? null,
                'last_name' => $validatedData['last_name'],
                'suffix' => $validatedData['suffix'] ?? null,
            ]);

            $staffData = [
                'staff_id' => $staffId,
                'member_id' => $member->member_id,
                'role_id' => $roleModel->role_id,
                'password' => Hash::make($validatedData['password']),
            ];

            if (isset($validatedData['profile_picture'])) {
                $file = $validatedData['profile_picture'];
                $path = $file->store('profile_pictures', 'public');
                $ext = $file->getClientOriginalExtension();

                $staffData['file_name'] = $path;
                $staffData['file_extension'] = '.'.$ext;
            }

            Staff::create($staffData);

            return $member->load(['staff.role', 'account.data']);
        });
    }
}
