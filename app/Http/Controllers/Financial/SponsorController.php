<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\Authentication\Account;
use App\Models\Operation\BudgetUpdate;
use App\Models\Operation\Data;
use App\Models\User\Member;
use App\Models\User\Sponsor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SponsorController extends Controller
{
    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $lastNum = $max ? (int) Str::afterLast($max, '-') : 0;

        return $base.'-'.Str::padLeft($lastNum + 1, 9, '0');
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 'all');
        $sortBy = $request->get('sort_by', 'latest');
        $search = $request->get('search', '');

        $query = Sponsor::select('sponsors.*', 'data.created_at as data_created_at')
            ->leftJoin('members', 'sponsors.member_id', '=', 'members.member_id')
            ->leftJoin('accounts', 'members.account_id', '=', 'accounts.account_id')
            ->leftJoin('data', 'accounts.data_id', '=', 'data.data_id')
            ->with(['member.account.data'])
            ->withSum(['budgetUpdates as total_amount_contributed' => function ($q) {
                $q->where('possessor', 'Sponsor')->where('reason', 'Sponsor Donation');
            }], 'amount_change');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $term = "%{$search}%";
                $q->where('members.full_name', 'like', $term)
                    ->orWhere('members.first_name', 'like', $term)
                    ->orWhere('members.last_name', 'like', $term);
            });
        }

        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('data_created_at', 'ASC');
                break;
            case 'name_asc':
                $query->orderBy('members.full_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('members.full_name', 'desc');
                break;
            case 'type_asc':
                $query->orderBy('sponsor_type', 'asc');
                break;
            case 'type_desc':
                $query->orderBy('sponsor_type', 'desc');
                break;
            default:
                $query->orderBy('data_created_at', 'DESC');
                break;
        }

        $collection = $query->distinct();

        if ($perPage === 'all') {
            $sponsors = $collection->get();
            $result = $sponsors->map(function ($s) {
                return [
                    'sponsor_id' => $s->sponsor_id,
                    'sponsor_type' => $s->sponsor_type,
                    'designation' => $s->designation,
                    'organization_name' => $s->organization_name,
                    'first_name' => $s->member->first_name ?? '',
                    'middle_name' => $s->member->middle_name ?? '',
                    'last_name' => $s->member->last_name ?? '',
                    'suffix' => $s->member->suffix ?? '',
                    'sponsor_name' => $s->sponsor_name,
                    'total_amount_contributed' => isset($s->total_amount_contributed) && $s->total_amount_contributed !== null ? (float) $s->total_amount_contributed : 0,
                    'created_at' => $s->member->account->data->created_at ?? null,
                ];
            })->values();

            return response()->json(['sponsors' => $result]);
        }

        $perPageInt = is_numeric($perPage) ? (int) $perPage : 5;
        $paginated = $collection->paginate($perPageInt);

        $result = collect($paginated->items())->map(function ($s) {
            return [
                'sponsor_id' => $s->sponsor_id,
                'sponsor_type' => $s->sponsor_type,
                'designation' => $s->designation,
                'organization_name' => $s->organization_name,
                'first_name' => $s->member->first_name ?? '',
                'middle_name' => $s->member->middle_name ?? '',
                'last_name' => $s->member->last_name ?? '',
                'suffix' => $s->member->suffix ?? '',
                'sponsor_name' => $s->sponsor_name,
                'total_amount_contributed' => isset($s->total_amount_contributed) && $s->total_amount_contributed !== null ? (float) $s->total_amount_contributed : 0,
                'created_at' => $s->member->account->data->created_at ?? null,
            ];
        })->values();

        return response()->json([
            'sponsors' => $result,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
        ]);
    }

    public function show(string $id)
    {
        $sponsor = Sponsor::with(['member.account.data'])
            ->withSum(['budgetUpdates as total_amount_contributed' => function ($q) {
                $q->where('possessor', 'Sponsor')->where('reason', 'Sponsor Donation');
            }], 'amount_change')
            ->find($id);

        if (! $sponsor) {
            return response()->json(['error' => 'Sponsor not found'], 404);
        }

        return response()->json($sponsor);
    }

    public function create(Request $request)
    {
        $request->validate([
            'sponsor_type' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'organization_name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $dataId = $this->generateDataId();
            $accountId = $this->generateAccountId();
            $memberId = $this->generateMemberId();
            $sponsorId = $this->generateNextId('SPNSR', 'sponsors', 'sponsor_id');

            Data::create(['data_id' => $dataId]);
            Account::create(['account_id' => $accountId, 'data_id' => $dataId, 'account_status' => 'Active', 'registered_at' => Carbon::now()]);
            Member::create(['member_id' => $memberId, 'account_id' => $accountId, 'first_name' => $request->first_name, 'middle_name' => $request->middle_name, 'last_name' => $request->last_name, 'suffix' => $request->suffix]);
            Sponsor::create([
                'sponsor_id' => $sponsorId,
                'member_id' => $memberId,
                'sponsor_type' => $request->sponsor_type,
                'designation' => $request->designation,
                'organization_name' => $request->organization_name,
            ]);

            DB::commit();

            return response()->json(['message' => 'Sponsor created successfully!']);
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'sponsor_type' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'organization_name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $sponsor = Sponsor::with('member')->find($id);

            if (! $sponsor) {
                DB::rollBack();

                return response()->json(['error' => 'Sponsor not found'], 404);
            }

            $member = $sponsor->member;

            if ($member) {
                $member->update([
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'suffix' => $request->suffix,
                ]);
            }

            $sponsor->update([
                'sponsor_type' => $request->sponsor_type,
                'designation' => $request->designation,
                'organization_name' => $request->organization_name,
            ]);

            DB::commit();

            return response()->json(['message' => 'Sponsor updated successfully!']);
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirmChanges(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.sponsor_id' => 'required|string|exists:sponsors,sponsor_id',
            'updates.*.sponsor_type' => 'nullable|string|max:255',
            'updates.*.designation' => 'nullable|string|max:255',
            'updates.*.organization_name' => 'nullable|string|max:255',
            'updates.*.first_name' => 'nullable|string|max:255',
            'updates.*.middle_name' => 'nullable|string|max:255',
            'updates.*.last_name' => 'nullable|string|max:255',
            'updates.*.suffix' => 'nullable|string|max:255',
            'deleted' => 'required|array',
            'deleted.*' => 'string|exists:sponsors,sponsor_id',
        ]);

        try {
            DB::beginTransaction();
            $updated = $request->input('updates');
            $deleted = $request->input('deleted');

            foreach ($updated as $item) {
                $sponsor = Sponsor::with('member')->find($item['sponsor_id']);
                if ($sponsor) {
                    $member = $sponsor->member;
                    if ($member) {
                        $member->update([
                            'first_name' => $item['first_name'] ?? $member->first_name,
                            'middle_name' => $item['middle_name'] ?? $member->middle_name,
                            'last_name' => $item['last_name'] ?? $member->last_name,
                            'suffix' => $item['suffix'] ?? $member->suffix,
                        ]);
                    }

                    $sponsor->update([
                        'sponsor_type' => $item['sponsor_type'] ?? $sponsor->sponsor_type,
                        'designation' => $item['designation'] ?? $sponsor->designation,
                        'organization_name' => $item['organization_name'] ?? $sponsor->organization_name,
                    ]);
                }
            }

            foreach ($deleted as $id) {
                $sponsor = Sponsor::with('member.account')->find($id);
                if ($sponsor) {
                    $budgetUpdateCount = BudgetUpdate::where('sponsor_id', $sponsor->sponsor_id)->count();
                    if ($budgetUpdateCount > 0) {
                        DB::rollBack();

                        return response()->json(['success' => false, 'error' => "Cannot delete Sponsor '{$sponsor->sponsor_name}' because {$budgetUpdateCount} budget update(s) are associated with it."]);
                    }
                    $memberId = $sponsor->member_id;
                    $accountId = $sponsor->member->account_id;
                    $dataId = $sponsor->member->account->data_id;
                    $sponsor->delete();
                    Member::where('member_id', $memberId)->delete();
                    Account::where('account_id', $accountId)->delete();
                    Data::where('data_id', $dataId)->delete();
                }
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function updateContributions(Request $request)
    {
        $validated = $request->validate([
            'contributions' => 'required|array',
            'sponsorId' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['contributions'] as $contributionData) {
                if (isset($contributionData['id']) && $contributionData['id']) {
                    $budgetUpdate = BudgetUpdate::find($contributionData['id']);

                    if ($budgetUpdate) {
                        $budgetUpdate->amount_change = $contributionData['amount'];
                        $budgetUpdate->amount_accum = 0;
                        $budgetUpdate->amount_before = 0;
                        $budgetUpdate->amount_recent = 0;
                        $budgetUpdate->amount_spent = 0;
                        $budgetUpdate->save();

                        if (! empty($contributionData['created_at'])) {
                            $createdAt = Carbon::parse($contributionData['created_at']);
                            $data = $budgetUpdate->data;
                            if ($data) {
                                $data->created_at = $createdAt;
                                $data->updated_at = $createdAt;
                                $data->save();
                            }
                        }
                    }
                } else {
                    $dataId = $this->generateDataId();
                    $budgetUpdateId = $this->generateBudgetUpdateId();

                    $createdAt = Carbon::now();
                    if (! empty($contributionData['created_at'])) {
                        try {
                            $createdAt = Carbon::parse($contributionData['created_at']);
                        } catch (Exception $e) {
                            $createdAt = Carbon::now();
                        }
                    }

                    Data::create([
                        'data_id' => $dataId,
                        'data_status' => 'Unarchived',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    BudgetUpdate::create([
                        'budget_update_id' => $budgetUpdateId,
                        'data_id' => $dataId,
                        'sponsor_id' => $validated['sponsorId'],
                        'possessor' => 'Sponsor',
                        'amount_accum' => 0,
                        'amount_recent' => 0,
                        'amount_before' => 0,
                        'amount_change' => $contributionData['amount'],
                        'amount_spent' => 0,
                        'direction' => 'Increase',
                        'reason' => 'Sponsor Donation',
                    ]);
                }
            }

            $budgetUpdateController = app()->make(BudgetUpdateController::class);
            $budgetUpdateController->recalculateBudgetHistory();

            DB::commit();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function generateDataId(): string
    {
        $year = Carbon::now()->year;
        $base = "DATA-{$year}";
        $last = Data::where('data_id', 'like', "{$base}-%")->latest('data_id')->value('data_id');
        $seq = $last ? (int) Str::substr($last, -9) : 0;

        return "{$base}-".Str::padLeft($seq + 1, 9, '0');
    }

    private function generateAccountId(): string
    {
        $year = Carbon::now()->year;
        $base = "ACCOUNT-{$year}";
        $last = Account::where('account_id', 'like', "{$base}-%")->latest('account_id')->value('account_id');
        $seq = $last ? (int) Str::substr($last, -9) : 0;

        return "{$base}-".Str::padLeft($seq + 1, 9, '0');
    }

    private function generateMemberId(): string
    {
        $year = Carbon::now()->year;
        $base = "MEMBER-{$year}";
        $last = Member::where('member_id', 'like', "{$base}-%")->latest('member_id')->value('member_id');
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
