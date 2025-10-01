<?php

namespace App\Livewire\Modal;

use Livewire\Component;
use App\Models\Authentication\Role;
use App\Models\User\Staff;
use App\Models\Storage\Data;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ModalRoles extends Component
{
    public $roles = [];
    public $newRoleName = '';
    public $newAllowedActions = [];
    public $newAccessScope = [];
    public $editingRoleId = null;
    public $editingRoleName = '';
    public $editingAllowedActions = [];
    public $editingAccessScope = [];
    public $isOpen = false;

    protected $listeners = [
        'loadRoles' => 'loadRoles',
        'openRolesModal' => 'openModal',
        'closeRolesModal' => 'closeModal'
    ];

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

    public function mount()
    {
        $this->loadRoles();
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->loadRoles();
    }

    public function loadRoles()
    {
        $optionsA = $this->allowedActionsOptions();
        $optionsS = $this->accessScopeOptions();
        $this->roles = Role::join('data', 'roles.data_id', '=', 'data.data_id')
            ->orderBy('data.updated_at', 'desc')
            ->get()
            ->map(function ($role) use ($optionsA, $optionsS) {
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
            })
            ->toArray();
    }

    public function addRole()
    {
        $this->validate(['newRoleName' => 'required|min:3']);
        DB::beginTransaction();
        try {
            $dataId = $this->generateNextId('DATA', 'data', 'data_id');
            Data::create([
                'data_id' => $dataId,
                'data_status' => 'Unarchived',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            Role::create([
                'role_id' => $this->generateNextId('ROLE', 'roles', 'role_id'),
                'data_id' => $dataId,
                'role' => $this->newRoleName,
                'allowed_actions' => collect($this->newAllowedActions)->join('. '),
                'access_scope' => collect($this->newAccessScope)->join('. ')
            ]);
            DB::commit();
            $this->newRoleName = '';
            $this->newAllowedActions = [];
            $this->newAccessScope = [];
            $this->loadRoles();
            $this->dispatch('showToast', ['message' => 'Role added', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('showToast', ['message' => 'Error adding role: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function startEdit($roleId)
    {
        $roleModel = Role::where('role_id', $roleId)->first();
        if (! $roleModel) {
            return;
        }
        $this->editingRoleId = $roleId;
        $this->editingRoleName = $roleModel->role;
        $this->editingAllowedActions = $this->matchOptionsFromString($roleModel->allowed_actions ?? '', $this->allowedActionsOptions());
        $this->editingAccessScope = $this->matchOptionsFromString($roleModel->access_scope ?? '', $this->accessScopeOptions());
    }

    public function cancelEdit()
    {
        $this->editingRoleId = null;
        $this->editingRoleName = '';
        $this->editingAllowedActions = [];
        $this->editingAccessScope = [];
    }

    public function updateRole()
    {
        $this->validate(['editingRoleName' => 'required|min:3']);
        DB::beginTransaction();
        try {
            $role = Role::where('role_id', $this->editingRoleId)->first();
            if ($role) {
                $role->update([
                    'role' => $this->editingRoleName,
                    'allowed_actions' => collect($this->editingAllowedActions)->join('. '),
                    'access_scope' => collect($this->editingAccessScope)->join('. ')
                ]);
                DB::commit();
                $this->cancelEdit();
                $this->loadRoles();
                $this->dispatch('showToast', ['message' => 'Role updated', 'type' => 'success']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('showToast', ['message' => 'Error updating role: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function deleteRole($roleId)
    {
        DB::beginTransaction();
        try {
            $role = Role::where('role_id', $roleId)->first();
            if (! $role) {
                $this->dispatch('showToast', ['message' => 'Role not found', 'type' => 'error']);
                return;
            }
            $staffCount = Staff::where('role_id', $role->role_id)->count();
            if ($staffCount > 0) {
                $this->dispatch('showToast', ['message' => "Cannot delete role '{$role->role}' because {$staffCount} staff member(s) are assigned to it.", 'type' => 'error']);
                return;
            }
            $dataId = $role->data_id;
            $role->delete();
            $referencing = DB::table('roles')->where('data_id', $dataId)->exists();
            if (! $referencing) {
                Data::where('data_id', $dataId)->delete();
            }
            DB::commit();
            $this->loadRoles();
            $this->dispatch('showToast', ['message' => 'Role deleted', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('showToast', ['message' => 'Error deleting role: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

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

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.modal.modal-roles', [
            'allowedActionsOptions' => $this->allowedActionsOptions(),
            'accessScopeOptions' => $this->accessScopeOptions()
        ]);
    }
}
