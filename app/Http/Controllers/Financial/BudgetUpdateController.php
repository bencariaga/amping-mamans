<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\Operation\BudgetUpdate;
use App\Models\Operation\Data;
use App\Models\User\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BudgetUpdateController extends Controller
{
    public function createForApplication($application, $assistanceAmount)
    {
        $prevBudget = BudgetUpdate::join('data', 'budget_updates.data_id', '=', 'data.data_id')
            ->orderBy('data.created_at', 'desc')
            ->select('budget_updates.*')
            ->first();

        $amount_accum = $prevBudget->amount_accum ?? 0;
        $prevAmountRecent = $prevBudget->amount_recent ?? 0;
        $prevAmountSpent = $prevBudget->amount_spent ?? 0;

        $amount_before = $prevAmountRecent;
        $amount_change = $assistanceAmount;
        $amount_recent = $amount_before - $amount_change;
        $amount_spent = $prevAmountSpent + $amount_change;

        $dataId = $this->generateDataId();
        $budgetUpdateId = $this->generateBudgetUpdateId();

        $budgetData = Data::create([
            'data_id' => $dataId,
            'data_status' => 'Unarchived',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $budgetUpdate = BudgetUpdate::create([
            'budget_update_id' => $budgetUpdateId,
            'data_id' => $budgetData->data_id,
            'sponsor_id' => null,
            'possessor' => 'AMPING',
            'amount_accum' => $amount_accum,
            'amount_recent' => $amount_recent,
            'amount_before' => $amount_before,
            'amount_change' => $amount_change,
            'amount_spent' => $amount_spent,
            'direction' => 'Decrease',
            'reason' => 'GL Release',
        ]);

        return $budgetUpdate;
    }

    public function getLatestBudget()
    {
        $increases = BudgetUpdate::where('direction', 'Increase')->sum('amount_change');
        $decreases_expenses = BudgetUpdate::where('direction', 'Decrease')->where('reason', '<>', 'Budget Manipulation')->sum('amount_change');
        $manipulations = BudgetUpdate::where('direction', 'Decrease')->where('reason', 'Budget Manipulation')->sum('amount_change');
        $hasSupplementaryBudget = BudgetUpdate::where('reason', 'Supplementary Budget')->exists();
        $allocated = (float) $increases - (float) $manipulations;
        $remaining = $allocated - (float) $decreases_expenses;

        return response()->json([
            'amount_accum' => (float) $allocated,
            'amount_change' => (float) $decreases_expenses,
            'amount_recent' => (float) $remaining,
            'has_supplementary_budget' => $hasSupplementaryBudget,
        ]);
    }

    public function list(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'latest');
        $perPage = (int) $request->input('per_page', 5);

        $query = BudgetUpdate::with(['sponsor.account.data', 'data.account.member']);

        if ($search) {
            $query->whereHas('sponsor.account.data', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($sortBy === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $budgetUpdates = $query->paginate($perPage);

        return response()->json($budgetUpdates);
    }

    public function create()
    {
        try {
            DB::beginTransaction();
            $dataId = $this->generateDataId();

            $budgetUpdateId = $this->generateBudgetUpdateId();

            Data::create([
                'data_id' => $dataId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            BudgetUpdate::create([
                'budget_update_id' => $budgetUpdateId,
                'data_id' => $dataId,
                'possessor' => 'AMPING',
                'amount_accum' => 0.00,
                'amount_recent' => 0.00,
                'amount_before' => 0.00,
                'amount_change' => 0.00,
                'direction' => 'Increase',
                'reason' => 'Supplementary Budget',
            ]);

            DB::commit();

            return response()->json(['success' => true, 'budget_update_id' => $budgetUpdateId]);
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createSupplementaryBudget(Request $request)
    {
        $validated = $request->validate([
            'amount_change' => 'required|numeric|min:0',
        ]);

        $increases = BudgetUpdate::where('direction', 'Increase')->sum('amount_change');
        $manipulations = BudgetUpdate::where('direction', 'Decrease')->where('reason', 'Budget Manipulation')->sum('amount_change');
        $allocated = (float) $increases - (float) $manipulations;

        $expenses = BudgetUpdate::where('direction', 'Decrease')->where('reason', '<>', 'Budget Manipulation')->sum('amount_change');
        $remaining = $allocated - $expenses;

        try {
            DB::beginTransaction();

            $dataId = $this->generateDataId();
            $budgetUpdateId = $this->generateBudgetUpdateId();

            Data::create([
                'data_id' => $dataId,
                'data_status' => 'Unarchived',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $amountChange = (float) $validated['amount_change'];

            BudgetUpdate::create([
                'budget_update_id' => $budgetUpdateId,
                'data_id' => $dataId,
                'possessor' => 'AMPING',
                'amount_accum' => $allocated + $amountChange,
                'amount_recent' => $remaining + $amountChange,
                'amount_before' => $allocated,
                'amount_change' => $amountChange,
                'amount_spent' => 0.00,
                'direction' => 'Increase',
                'reason' => 'Supplementary Budget',
            ]);

            DB::commit();

            return response()->json(['success' => true, 'budget_update_id' => $budgetUpdateId]);
        } catch (\Exception $e) {
            DB::rollBack();

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
            DB::beginTransaction();

            $dataId = $this->generateDataId();
            $budgetUpdateId = $this->generateBudgetUpdateId();

            Data::create([
                'data_id' => $dataId,
                'data_status' => 'Unarchived',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $amountSpent = 0.00;
            $amountAccum = 0.00;

            if ($validated['direction'] === 'Increase') {
                $amountAccum = (float) $validated['amount_change'];
                $amountSpent = 0.00;
            } else {
                $amountSpent = (float) $validated['amount_change'];
                $amountAccum = 0.00;
            }

            $payload = [
                'budget_update_id' => $budgetUpdateId,
                'data_id' => $dataId,
                'possessor' => $validated['possessor'],
                'amount_accum' => $amountAccum,
                'amount_recent' => isset($validated['amount_recent']) ? (float) $validated['amount_recent'] : 0.00,
                'amount_before' => isset($validated['amount_before']) ? (float) $validated['amount_before'] : 0.00,
                'amount_change' => (float) $validated['amount_change'],
                'amount_spent' => $amountSpent,
                'direction' => $validated['direction'],
                'reason' => $validated['reason'],
            ];

            if ($validated['possessor'] === 'Sponsor' && ! empty($validated['sponsor_id'])) {
                $payload['sponsor_id'] = $validated['sponsor_id'];
            }

            BudgetUpdate::create($payload);

            DB::commit();

            return response()->json(['success' => true, 'budget_update_id' => $budgetUpdateId]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|array',
            'data.*.budget_update_id' => 'required|string',
            'data.*.field' => 'required|string',
            'data.*.value' => 'required',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['data'] as $item) {
                $budgetUpdate = BudgetUpdate::findOrFail($item['budget_update_id']);
                $field = $item['field'];
                $value = $item['value'];

                if ($field === 'amount_change' && is_numeric($value)) {
                    $budgetUpdate->update(['amount_change' => $value]);
                } elseif ($field === 'reason') {
                    $budgetUpdate->update(['reason' => $value]);
                }
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function applyChanges(Request $request)
    {
        try {
            DB::beginTransaction();
            $updated = $request->input('updated', []);
            $deleted = $request->input('deleted', []);

            foreach ($updated as $item) {
                $budgetUpdate = BudgetUpdate::findOrFail($item['id']);

                if ($budgetUpdate) {
                    $oldAmount = $budgetUpdate->amount_change;
                    $newAmount = $item['amount_change'];
                    $oldReason = $budgetUpdate->reason;
                    $newReason = $item['reason'];

                    if ($oldAmount != $newAmount) {
                        $budgetUpdate->amount_change = $newAmount;
                        $budgetUpdate->save();
                    }

                    if ($oldReason != $newReason) {
                        $budgetUpdate->reason = $newReason;
                        $budgetUpdate->save();
                    }
                }
            }

            foreach ($deleted as $id) {
                $budgetUpdate = BudgetUpdate::find($id);
                if ($budgetUpdate) {
                    $dataId = $budgetUpdate->data_id;
                    $budgetUpdate->delete();
                    Data::where('data_id', $dataId)->delete();
                }
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function showContributionTable($id)
    {
        $sponsor = Sponsor::with(['member.account.data'])->find($id);

        if (! $sponsor) {
            abort(404);
        }

        $contributions = BudgetUpdate::with('data')
            ->where('sponsor_id', $id)
            ->where('possessor', 'Sponsor')
            ->where('reason', 'Sponsor Donation')
            ->get()
            ->sortBy(function ($contribution) {
                return optional($contribution->data)->created_at;
            })
            ->values();

        $runningTotal = 0;

        $contributions = $contributions->map(function ($item) use (&$runningTotal) {
            $runningTotal += $item->amount_change;
            $item->total_amount = $runningTotal;

            $item->amount_spent = 0.00;
            $item->amount_accum = $item->amount_change;

            return $item;
        });

        return view('pages.sidebar.contribution.contribution-tables', [
            'contributions' => $contributions,
            'id' => $id,
            'sponsors' => collect([$sponsor]),
        ]);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $budgetUpdate = BudgetUpdate::find($id);

            if (! $budgetUpdate) {
                return response()->json(['success' => false, 'error' => 'Contribution not found.'], 404);
            }

            $dataId = $budgetUpdate->data_id;
            $budgetUpdate->delete();
            Data::where('data_id', $dataId)->delete();
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function recalculateBudgetHistory()
    {
        $updates = BudgetUpdate::with('data')->get()->sortBy(function ($u) {
            return optional($u->data)->created_at ?: Carbon::create(1970, 1, 1);
        });

        $accum = 0.00;
        $recent = 0.00;

        foreach ($updates as $update) {
            $update->amount_before = $accum;

            if ($update->direction === 'Increase') {
                $accum = (float) $accum + (float) $update->amount_change;
                $recent = (float) $recent + (float) $update->amount_change;
            } else {
                $accum = (float) $accum - (float) $update->amount_change;
                $recent = (float) $recent - (float) $update->amount_change;
            }

            $update->amount_accum = $accum;
            $update->amount_recent = $recent;
            $update->save();
        }

        return true;
    }

    private function generateDataId(): string
    {
        $year = Carbon::now()->year;
        $base = "DATA-{$year}";
        $last = Data::where('data_id', 'like', "{$base}-%")->latest('data_id')->value('data_id');
        $seq = $last ? (int) Str::substr($last, -9) : 0;

        return "{$base}-".Str::padLeft($seq + 1, 9, '0');
    }

    private function generateBudgetUpdateId(): string
    {
        $year = Carbon::now()->year;
        $base = "BDG-UPD-{$year}";
        $last = BudgetUpdate::where('budget_update_id', 'like', "{$base}-%")->latest('budget_update_id')->value('budget_update_id');
        $seq = $last ? (int) Str::substr($last, -9) : 0;

        return "{$base}-".Str::padLeft($seq + 1, 9, '0');
    }
}
