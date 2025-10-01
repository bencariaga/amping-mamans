<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Authentication\Account;
use App\Models\Authentication\Role;
use App\Models\Storage\Data;
use App\Models\User\Member;
use App\Models\User\Staff;
use App\Models\Storage\File;

class UserRegistrationController extends Controller
{
    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max  = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $lastNum = $max ? (int) Str::afterLast($max, '-') : 0;
        return $base . '-' . Str::padLeft($lastNum + 1, 9, '0');
    }

    public function create()
    {
        $roles = Role::join('data', 'roles.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get();
        return view('pages.sidebar.profiles.register.user', ['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $request->validate([
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
            ->where('first_name', $request->first_name)
            ->where('middle_name', $request->middle_name)
            ->where('last_name', $request->last_name)
            ->exists();

        if ($existsUser) {
            return back()->withInput()->withErrors([
                'duplicate_user' => 'This user account already exists with the same name.'
            ]);
        }

        DB::beginTransaction();
        try {
            $dataId   = $this->generateNextId('DATA',    'data',     'data_id');
            $acctId   = $this->generateNextId('ACCOUNT', 'accounts', 'account_id');
            $memId    = $this->generateNextId('MEMBER',  'members',  'member_id');
            $staffId  = $this->generateNextId('STAFF',   'staff',    'staff_id');

            $data = Data::create([
                'data_id'     => $dataId,
                'data_status' => 'Unarchived',
            ]);

            $acct = Account::create([
                'account_id'     => $acctId,
                'data_id'        => $data->data_id,
                'account_status' => 'Active',
                'registered_at'  => Carbon::now(),
            ]);

            if ($request->filled('custom_role')) {
                $data2 = Data::create([
                    'data_id'     => $this->generateNextId('DATA', 'data', 'data_id'),
                    'data_status' => 'Unarchived',
                ]);
                $roleModel = Role::create([
                    'role_id' => $this->generateNextId('ROLE', 'roles', 'role_id'),
                    'data_id' => $data2->data_id,
                    'role'    => $request->custom_role,
                ]);
            } else {
                $roleModel = Role::findOrFail($request->role_id);
            }

            $member = Member::create([
                'member_id' => $memId,
                'account_id' => $acct->account_id,
                'member_type' => 'Staff',
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'suffix' => $request->suffix,
                'full_name' => Str::of("{$request->first_name} {$request->middle_name} {$request->last_name} {$request->suffix}")->trim(),
            ]);

            Staff::create([
                'staff_id' => $staffId,
                'member_id' => $member->member_id,
                'role_id' => $roleModel->role_id,
                'password' => Hash::make($request->password),
            ]);

            if ($file = $request->file('profile_picture')) {
                $rand = Str::random(16);
                $ext = $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_pictures', "{$rand}.{$ext}", 'public');
                $fileDataId = $this->generateNextId('DATA', 'data', 'data_id');
                $fileId = $this->generateNextId('FILE', 'files', 'file_id');

                Data::create([
                    'data_id' => $fileDataId,
                    'data_status' => 'Unarchived',
                ]);

                File::create([
                    'file_id' => $fileId,
                    'data_id' => $fileDataId,
                    'member_id' => $member->member_id,
                    'file_type' => 'Image',
                    'filename' => $path,
                    'file_extension' => $ext,
                ]);
            }

            DB::commit();
            return redirect()->route('profiles.users.list')->with('success', 'User account has been added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}
