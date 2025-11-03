<?php

namespace App\Http\Controllers\Core;

use App\Actions\Household\CreateHousehold;
use App\Actions\Household\DeleteHousehold;
use App\Actions\Household\UpdateHousehold;
use App\Actions\Miscellaneous\GetAccessScopeOptions;
use App\Actions\Miscellaneous\GetAffiliatePartners;
use App\Actions\Miscellaneous\GetAllowedActionsOptions;
use App\Actions\Miscellaneous\GetHouseholds;
use App\Actions\Miscellaneous\GetOccupations;
use App\Actions\Miscellaneous\GetRoles;
use App\Actions\Miscellaneous\GetServices;
use App\Actions\Miscellaneous\GetSponsors;
use App\Actions\Occupation\CreateOccupation;
use App\Actions\Occupation\DeleteOccupation;
use App\Actions\Occupation\UpdateOccupation;
use App\Actions\Role\CreateRole;
use App\Actions\Role\DeleteRole;
use App\Actions\Role\UpdateRole;
use App\Actions\Service\CreateService;
use App\Actions\Service\DeleteService;
use App\Actions\Service\UpdateService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MiscellaneousController extends Controller
{
    public function index(): View
    {
        return view('pages.dashboard.templates.miscellaneous', [
            'roles' => GetRoles::execute(),
            'occupations' => GetOccupations::execute(),
            'services' => GetServices::execute(),
            'affiliatePartners' => GetAffiliatePartners::execute(),
            'sponsors' => GetSponsors::execute(),
            'households' => GetHouseholds::execute(),
            'allowedActionsOptions' => GetAllowedActionsOptions::execute(),
            'accessScopeOptions' => GetAccessScopeOptions::execute(),
        ]);
    }

    public function storeRole(Request $request, CreateRole $createRole): JsonResponse
    {
        $request->validate(['role' => 'required|string|min:3|max:100']);

        try {
            $allowedActions = $request->input('allowed_actions', []);
            $accessScope = $request->input('access_scope', []);

            $role = $createRole->execute([
                'role' => $request->role,
                'allowed_actions' => is_array($allowedActions) ? implode('. ', $allowedActions) : $allowedActions,
                'access_scope' => is_array($accessScope) ? implode('. ', $accessScope) : $accessScope,
            ]);

            return response()->json(['success' => true, 'role' => $role]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function updateRole(Request $request, $id, UpdateRole $updateRole): JsonResponse
    {
        $request->validate(['role' => 'required|string|min:3|max:100']);

        try {
            $allowedActions = $request->input('allowed_actions', []);
            $accessScope = $request->input('access_scope', []);

            $role = $updateRole->execute($id, [
                'role' => $request->role,
                'allowed_actions' => is_array($allowedActions) ? implode('. ', $allowedActions) : $allowedActions,
                'access_scope' => is_array($accessScope) ? implode('. ', $accessScope) : $accessScope,
            ]);

            return response()->json(['success' => true, 'role' => $role]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function destroyRole($id, DeleteRole $deleteRole): JsonResponse
    {
        try {
            $deleteRole->execute($id);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function storeOccupation(Request $request, CreateOccupation $createOccupation): JsonResponse
    {
        $request->validate(['occupation' => 'required|string|max:30']);

        try {
            $occupation = $createOccupation->execute($request->occupation);

            return response()->json(['success' => true, 'occupation' => $occupation]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function updateOccupation(Request $request, $id, UpdateOccupation $updateOccupation): JsonResponse
    {
        $request->validate(['occupation' => 'required|string|max:30']);

        try {
            $occupation = $updateOccupation->execute($id, $request->occupation);

            return response()->json(['success' => true, 'occupation' => $occupation]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function destroyOccupation($id, DeleteOccupation $deleteOccupation): JsonResponse
    {
        try {
            $deleteOccupation->execute($id);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function storeService(Request $request, CreateService $createService): JsonResponse
    {
        $request->validate(['service' => 'required|string|max:20']);

        try {
            $service = $createService->execute($request->service);

            return response()->json(['success' => true, 'service' => $service]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function updateService(Request $request, $id, UpdateService $updateService): JsonResponse
    {
        $request->validate(['service' => 'required|string|max:20']);

        try {
            $service = $updateService->execute($id, $request->service);

            return response()->json(['success' => true, 'service' => $service]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function destroyService($id, DeleteService $deleteService): JsonResponse
    {
        try {
            $deleteService->execute($id);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function storeHousehold(Request $request, CreateHousehold $createHousehold): JsonResponse
    {
        $request->validate(['household_name' => 'required|string|max:100']);

        try {
            $household = $createHousehold->execute($request->household_name);

            return response()->json(['success' => true, 'household' => $household]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function updateHousehold(Request $request, $id, UpdateHousehold $updateHousehold): JsonResponse
    {
        $request->validate(['household_name' => 'required|string|max:100']);

        try {
            $household = $updateHousehold->execute($id, $request->household_name);

            return response()->json(['success' => true, 'household' => $household]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function destroyHousehold($id, DeleteHousehold $deleteHousehold): JsonResponse
    {
        try {
            $deleteHousehold->execute($id);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }
}
