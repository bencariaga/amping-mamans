<?php

namespace App\Http\Controllers\Core;

use App\Actions\Core\CreateRole;
use App\Actions\Core\DeleteRole;
use App\Actions\Core\UpdateRole;
use App\Http\Controllers\Controller;
use App\Models\Authentication\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleController extends Controller
{

    private function allowedActionsOptions(): array
    {
        return [
            'Create, view, edit, and deactivate accounts of staff members, applicants, sponsors, affiliate partners, and services',
            "Create, view, edit, archive, download, and print reports, including the AMPING's financial status and user activity data",
            'Create, view, edit, archive, download, and print templates for assistance request forms, guarantee letters, and text messages',
            'Create, view, edit, and delete tariff lists and change the version of tariff lists to use for assistance amount calculation',
            'Create, view, edit, and delete staff role names and client occupation names',
            'Assign and reassigned roles to staff members',
            'Approve or reject assistance requests and authorize guarantee letters',
            'Send text messages to applicants with approved guarantee letters',
            'Update, add to, and monitor the program budget from government funds, sponsors, and other sources',
            'Delete system cache and log data when necessary',
            'View and use assistance request templates to create assistance request forms',
            "View the AMPING's financial status, including the program budget sources from government funds, sponsors, and other sources",
            'View the staff role names and client occupation names',
            'View the roles of staff members',
            'View the version of tariff lists to use for assistance amount calculation',
            'View and use guarantee letter templates to create guarantee letters',
            'View accounts of staff members, applicants, sponsors, affiliate partners, and services',
            'View and use text message templates to create text messages',
        ];
    }

    private function accessScopeOptions(): array
    {
        return [
            'Full access to every web page, every feature, and every module, without restrictions',
            'Full access to profiles and system activities of staff members, applicants, patients, sponsors, and affiliate partners',
            'Full access to templates for assistance request forms, guarantee letters, and text messages',
            'Full access to financial records, such as budgets, expenses, and funding sources',
            'Full access to staff role and client occupation names, and tariff lists',
            'Full access to staff role and tariff list adjustments',
            'Full access to data and account archiving, deletion, and deactivation',
            'Full access to logs and reports',
            'Access limited to viewing and editing account profiles',
            'Access limited to viewing templates for assistance request forms',
            'Access limited to viewing financial records, such as budgets, expenses, and funding sources',
            'Access limited to viewing staff roles, client occupations, and tariff list versions',
            'Access limited to viewing account profiles',
            'Access limited to viewing templates for guarantee letters',
            'Access limited to approving and rejecting assistance requests and authorizing guarantee letters',
            'Access limited to viewing templates for text messages',
            'Access limited to sending text messages to applicants with approved guarantee letters',
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
            'roles' => ['required', 'array'],
            'roles.*' => ['nullable', 'string', 'exists:roles,role_id'],
        ]);

        foreach ($request->input('roles') as $memberId => $roleId) {
            DB::table('staff')->where('member_id', $memberId)->update(['role_id' => $roleId]);
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
                'access_scope_list' => $this->matchOptionsFromString($scope, $optionsS),
            ];
        });

        return response()->json($roles);
    }

    public function confirmChanges(Request $request, CreateRole $createRole, UpdateRole $updateRole, DeleteRole $deleteRole)
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

                $createRole->execute([
                    'role' => (string) $roleName,
                    'allowed_actions' => $roleData['allowed_actions'] ?? null,
                    'access_scope' => $roleData['access_scope'] ?? null,
                ]);
            }

            foreach ($updates as $roleData) {
                if (!is_array($roleData) || empty($roleData['role_id']) || empty($roleData['role'])) {
                    continue;
                }

                $roleName = Str::of($roleData['role'])->trim();

                if ($roleName === '') {
                    continue;
                }

                $updateRole->execute($roleData['role_id'], [
                    'role' => (string) $roleName,
                    'allowed_actions' => $roleData['allowed_actions'] ?? null,
                    'access_scope' => $roleData['access_scope'] ?? null,
                ]);
            }

            foreach ($deletes as $roleId) {
                if (!is_string($roleId) || Str::of($roleId)->trim() === '') {
                    continue;
                }

                $deleteRole->execute($roleId);
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
                    'access_scope_list' => $this->matchOptionsFromString($scope, $optionsS),
                ];
            });

            return response()->json([
                'success' => true,
                'roles' => $updatedRoles,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id, DeleteRole $deleteRole)
    {
        DB::beginTransaction();

        try {
            $deleteRole->execute($id);
            DB::commit();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
