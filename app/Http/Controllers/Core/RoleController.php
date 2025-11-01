<?php

namespace App\Http\Controllers\Core;

use App\Actions\Core\Role\DeleteRole;
use App\Actions\Core\Role\FormatRolesWithOptions;
use App\Actions\Core\Role\GetRolesWithData;
use App\Actions\Core\Role\ProcessRoleChanges;
use App\Actions\Core\Role\UpdateUserRoles;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        private GetRolesWithData $getRolesWithData,
        private UpdateUserRoles $updateUserRoles,
        private FormatRolesWithOptions $formatRolesWithOptions,
        private ProcessRoleChanges $processRoleChanges,
        private DeleteRole $deleteRole
    ) {}


    public function edit(Request $request)
    {
        $roles = $this->getRolesWithData->execute();

        return view('pages.sidebar.profiles.register.user', ['roles' => $roles]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['nullable', 'string', 'exists:roles,role_id'],
        ]);

        $this->updateUserRoles->execute($request->input('roles'));

        return redirect()->route('profiles.users.list')->with('success', 'User roles have been updated successfully.');
    }

    public function index()
    {
        $roles = $this->getRolesWithData->execute();
        $formattedRoles = $this->formatRolesWithOptions->execute($roles);

        return response()->json($formattedRoles);
    }

    public function confirmChanges(Request $request)
    {
        $payload = $request->all();
        $creates = isset($payload['create']) && is_array($payload['create']) ? $payload['create'] : [];
        $updates = isset($payload['update']) && is_array($payload['update']) ? $payload['update'] : [];
        $deletes = isset($payload['delete']) && is_array($payload['delete']) ? $payload['delete'] : [];

        try {
            $this->processRoleChanges->execute($creates, $updates, $deletes);

            $updatedRoles = $this->getRolesWithData->execute();
            $formattedRoles = $this->formatRolesWithOptions->execute($updatedRoles);

            return response()->json([
                'success' => true,
                'roles' => $formattedRoles,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $this->deleteRole->execute($id);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
