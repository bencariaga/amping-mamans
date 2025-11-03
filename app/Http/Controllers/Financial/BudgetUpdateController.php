<?php

namespace App\Http\Controllers\Financial;

use App\Actions\Budget\ApplyBudgetChanges;
use App\Actions\Budget\CreateBudgetForApplication;
use App\Actions\Budget\CreateBudgetUpdate;
use App\Actions\Budget\CreateSupplementaryBudget;
use App\Actions\Budget\DeleteBudgetUpdate;
use App\Actions\Budget\GetLatestBudget;
use App\Actions\Budget\GetSponsorContributions;
use App\Actions\Budget\RecalculateBudgetHistory;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class BudgetUpdateController extends Controller
{
    public function createForApplication($application, $assistanceAmount)
    {
        $action = new CreateBudgetForApplication;

        return $action->execute($assistanceAmount);
    }

    public function getLatestBudget()
    {
        $action = new GetLatestBudget;
        $data = $action->execute();

        return response()->json($data);
    }

    public function createSupplementaryBudget(Request $request)
    {
        $validated = $request->validate([
            'amount_change' => 'required|numeric|min:0',
        ]);

        try {
            $action = new CreateSupplementaryBudget;
            $budgetUpdate = $action->execute((float) $validated['amount_change']);

            return response()->json(['success' => true, 'budget_update_id' => $budgetUpdate->budget_update_id]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount_change' => 'required|numeric',
            'possessor' => 'required|in:AMPING,Sponsor',
            'reason' => 'required|string',
            'direction' => 'required|in:Increase,Decrease',
            'sponsor_id' => 'nullable|string',
            'amount_before' => 'nullable|numeric',
            'amount_recent' => 'nullable|numeric',
            'amount_accum' => 'nullable|numeric',
        ]);

        try {
            $action = new CreateBudgetUpdate;
            $budgetUpdate = $action->execute($validated);

            return response()->json(['success' => true, 'budget_update_id' => $budgetUpdate->budget_update_id]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function applyChanges(Request $request)
    {
        try {
            $updated = $request->input('updated', []);
            $deleted = $request->input('deleted', []);
            $action = new ApplyBudgetChanges;
            $action->execute($updated, $deleted);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function showContributionTable($id)
    {
        $action = new GetSponsorContributions;
        $result = $action->execute($id);

        if (! $result['sponsor']) {
            abort(404);
        }

        return view('pages.dashboard.templates.miscellaneous', [
            'contributions' => $result['contributions'],
            'id' => $id,
            'sponsors' => collect([$result['sponsor']]),
        ]);
    }

    public function destroy($id)
    {
        try {
            $action = new DeleteBudgetUpdate;
            $action->execute($id);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function recalculateBudgetHistory()
    {
        $action = new RecalculateBudgetHistory;

        return $action->execute();
    }
}
