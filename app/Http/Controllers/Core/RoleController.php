<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Authentication\Role;
use App\Models\User\Staff;
use App\Models\Storage\Data;
use Exception;

class RoleController extends Controller
{
    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max  = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $lastNum = $max ? (int) Str::afterLast($max, '-') : 0;
        $next = $lastNum + 1;
        $padded = Str::padLeft($next, 9, '0');
        return "{$base}-{$padded}";
    }

    private function allowedActionsOptions(): array
    {
        return [
            "Create, view, edit, and deactivate accounts of staff members, applicants, sponsors, affiliate partners, and services",
            "Create, view, edit, archive, download, and print reports, including the AMPING's financial status and user activity data",
            "Create, view, edit, archive, download, and print templates for assistance request forms, guarantee letters, and text messages",
            "Create, view, edit, and delete tariff lists and change the version of tariff lists to use for assistance amount calculation",
            "Create, view, edit, and delete staff role names and client occupation names",
            "Assign and reassigned roles to staff members",
            "Approve or reject assistance requests and authorize guarantee letters",
            "Send text messages to applicants with approved guarantee letters",
            "Update, add to, and monitor the program budget from government funds, sponsors, and other sources",
            "Delete system cache and log data when necessary",
            "View and use assistance request templates to create assistance request forms",
            "View the AMPING's financial status, including the program budget sources from government funds, sponsors, and other sources",
            "View the staff role names and client occupation names",
            "View the roles of staff members",
            "View the version of tariff lists to use for assistance amount calculation",
            "View and use guarantee letter templates to create guarantee letters",
            "View accounts of staff members, applicants, sponsors, affiliate partners, and services",
            "View and use text message templates to create text messages"
        ];
    }

    private function accessScopeOptions(): array
    {
        return [
            "Full access to every web page, every feature, and every module, without restrictions",
            "Full access to profiles and system activities of staff members, applicants, patients, sponsors, and affiliate partners",
            "Full access to templates for assistance request forms, guarantee letters, and text messages",
            "Full access to financial records, such as budgets, expenses, and funding sources",
            "Full access to staff role and client occupation names, and tariff lists",
            "Full access to staff role and tariff list adjustments",
            "Full access to data and account archiving, deletion, and deactivation",
            "Full access to logs and reports",
            "Access limited to viewing and editing account profiles",
            "Access limited to viewing templates for assistance request forms",
            "Access limited to viewing financial records, such as budgets, expenses, and funding sources",
            "Access limited to viewing staff roles, client occupations, and tariff list versions",
            "Access limited to viewing account profiles",
            "Access limited to viewing templates for guarantee letters",
            "Access limited to approving and rejecting assistance requests and authorizing guarantee letters",
            "Access limited to viewing templates for text messages",
            "Access limited to sending text messages to applicants with approved guarantee letters"
        ];
    }

    private function matchOptionsFromString(?string $stored, array $options): array
    {
        $result = [];

        if ($stored === null || $stored === '') {
            return $result;
        }

        $hay = mb_strtolower($stored);

        foreach ($options as $opt) {
            if ($opt === null) {
                continue;
            }

            $needle = mb_strtolower($opt);

            if (mb_strpos($hay, $needle) !== false) {
                $result[] = $opt;
            }
        }

        return $result;
    }

    public function edit(Request $request)
    {
        $roles = Role::join('data', 'roles.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get();
        return view('pages.sidebar.profiles.register.user', ['roles' => $roles]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'roles'   => ['required', 'array'],
            'roles.*' => ['nullable', 'string', 'exists:roles,role_id'],
        ]);

        foreach ($request->input('roles') as $memberId => $roleId) {
            Staff::where('member_id', $memberId)->update(['role_id' => $roleId]);
        }

        return redirect()->route('profiles.users.list')->with('success', 'User roles have been updated successfully.');
    }

    public function index()
    {
        $optionsA = $this->allowedActionsOptions();
        $optionsS = $this->accessScopeOptions();

        $roles = Role::join('data', 'roles.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get()->map(function ($role) use ($optionsA, $optionsS) {
            $allowed = $role->allowed_actions ?? '';
            $scope = $role->access_scope ?? '';

            return [
                'role_id' => $role->role_id,
                'data_id' => $role->data_id,
                'role' => $role->role,
                'allowed_actions' => $allowed,
                'allowed_actions_list' => $this->matchOptionsFromString($allowed, $optionsA),
                'access_scope' => $scope,
                'access_scope_list' => $this->matchOptionsFromString($scope, $optionsS)
            ];
        });

        return response()->json($roles);
    }

    public function confirmChanges(Request $request)
    {
        $payload = $request->all();
        $creates = isset($payload['create']) && is_array($payload['create']) ? $payload['create'] : [];
        $updates = isset($payload['update']) && is_array($payload['update']) ? $payload['update'] : [];
        $deletes = isset($payload['delete']) && is_array($payload['delete']) ? $payload['delete'] : [];

        DB::beginTransaction();

        try {
            foreach ($creates as $roleData) {
                if (!is_array($roleData) || empty($roleData['role'])) {
                    continue;
                }

                $roleName = Str::of($roleData['role'])->trim();

                if ($roleName === '') {
                    continue;
                }

                $dataId = $this->generateNextId('DATA', 'data', 'data_id');
                Data::create(['data_id' => $dataId, 'data_status' => 'Unarchived']);

                Role::create([
                    'role_id' => $this->generateNextId('ROLE', 'roles', 'role_id'),
                    'data_id' => $dataId,
                    'role' => (string) $roleName,
                    'allowed_actions' => isset($roleData['allowed_actions']) ? $roleData['allowed_actions'] : null,
                    'access_scope' => isset($roleData['access_scope']) ? $roleData['access_scope'] : null
                ]);
            }

            foreach ($updates as $roleData) {
                if (!is_array($roleData) || empty($roleData['role_id']) || empty($roleData['role'])) {
                    continue;
                }

                $roleId = $roleData['role_id'];
                $roleName = Str::of($roleData['role'])->trim();

                if ($roleName === '') {
                    continue;
                }

                Role::where('role_id', $roleId)->update([
                    'role' => (string) $roleName,
                    'allowed_actions' => isset($roleData['allowed_actions']) ? $roleData['allowed_actions'] : null,
                    'access_scope' => isset($roleData['access_scope']) ? $roleData['access_scope'] : null
                ]);
            }

            foreach ($deletes as $roleId) {
                if (!is_string($roleId) || Str::of($roleId)->trim() === '') {
                    continue;
                }

                $role = Role::where('role_id', $roleId)->first();

                if ($role) {
                    $staffCount = Staff::where('role_id', $role->role_id)->count();

                    if ($staffCount > 0) {
                        throw new Exception("Cannot delete role '{$role->role}' because {$staffCount} staff member(s) are assigned to it.");
                    }

                    $dataId = $role->data_id;
                    $role->delete();
                    $referencing = false;
                    $tablesToCheck = ['roles'];

                    foreach ($tablesToCheck as $table) {
                        if (DB::table($table)->where('data_id', $dataId)->exists()) {
                            $referencing = true;
                            break;
                        }
                    }

                    if (!$referencing) {
                        Data::where('data_id', $dataId)->delete();
                    }
                }
            }

            DB::commit();

            $optionsA = $this->allowedActionsOptions();
            $optionsS = $this->accessScopeOptions();

            $updatedRoles = Role::join('data', 'roles.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get()->map(function ($role) use ($optionsA, $optionsS) {
                $allowed = $role->allowed_actions ?? '';
                $scope = $role->access_scope ?? '';

                return [
                    'role_id' => $role->role_id,
                    'data_id' => $role->data_id,
                    'role' => $role->role,
                    'allowed_actions' => $allowed,
                    'allowed_actions_list' => $this->matchOptionsFromString($allowed, $optionsA),
                    'access_scope' => $scope,
                    'access_scope_list' => $this->matchOptionsFromString($scope, $optionsS)
                ];
            });

            return response()->json([
                'success' => true,
                'roles' => $updatedRoles
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $role = Role::where('role_id', $id)->first();

            if ($role) {
                $staffCount = Staff::where('role_id', $role->role_id)->count();

                if ($staffCount > 0) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'error' => "Cannot delete role '{$role->role}' because {$staffCount} staff member(s) are assigned to it."]);
                }

                $dataId = $role->data_id;
                $role->delete();
                $referencing = false;
                $tablesToCheck = ['roles'];

                foreach ($tablesToCheck as $table) {
                    if (DB::table($table)->where('data_id', $dataId)->exists()) {
                        $referencing = true;
                        break;
                    }
                }

                if (!$referencing) {
                    Data::where('data_id', $dataId)->delete();
                }

                DB::commit();
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'error' => 'Role not found.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
