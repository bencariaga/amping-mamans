<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Authentication\Account;
use App\Models\User\AffiliatePartner;
use App\Models\User\Sponsor;
use App\Models\User\Staff;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function deactivated(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type', 'all');
        $sortBy = $request->input('sort_by', 'latest');
        $perPage = $request->input('per_page', 5);

        $accounts = collect();

        if ($type === 'all' || $type === 'staff') {
            $staff = Account::with(['members.staff', 'data'])
                ->where('account_status', 'Deactivated')
                ->whereHas('members.staff')
                ->get()
                ->map(function ($account) {
                    $member = $account->members->first();
                    $name = $member ? "{$member->last_name}, {$member->first_name}" : 'N/A';
                    $staffId = $member->staff->staff_id ?? null;

                    return (object) [
                        'account_id' => $account->account_id,
                        'type' => 'Staff',
                        'name' => $name,
                        'deactivated_at' => $account->data->updated_at ?? null,
                        'member_id_for_link' => $staffId,
                    ];
                });
            $accounts = $accounts->merge($staff);
        }

        if ($type === 'all' || $type === 'applicants') {
            $applicants = Account::with(['members.client.applicant', 'data'])
                ->where('account_status', 'Deactivated')
                ->whereHas('members.client.applicant')
                ->get()
                ->map(function ($account) {
                    $member = $account->members->first();
                    $name = $member ? "{$member->last_name}, {$member->first_name}" : 'N/A';
                    $applicantId = $member->client->applicant->applicant_id ?? null;

                    return (object) [
                        'account_id' => $account->account_id,
                        'type' => 'Applicant',
                        'name' => $name,
                        'deactivated_at' => $account->data->updated_at ?? null,
                        'member_id_for_link' => $applicantId,
                    ];
                });
            $accounts = $accounts->merge($applicants);
        }

        if ($type === 'all' || $type === 'sponsors') {
            $sponsors = Account::with(['thirdParties.sponsor', 'data'])
                ->where('account_status', 'Deactivated')
                ->whereHas('thirdParties.sponsor')
                ->get()
                ->map(function ($account) {
                    $tp = $account->thirdParties->first();
                    $sponsor = $tp->sponsor ?? null;
                    $name = $sponsor ? ($sponsor->organization_name ?? 'N/A') : 'N/A';
                    $sponsorId = $sponsor->sponsor_id ?? null;

                    return (object) [
                        'account_id' => $account->account_id,
                        'type' => 'Sponsor',
                        'name' => $name,
                        'deactivated_at' => $account->data->updated_at ?? null,
                        'member_id_for_link' => $sponsorId,
                    ];
                });
            $accounts = $accounts->merge($sponsors);
        }

        if ($type === 'all' || $type === 'affiliate_partners') {
            $partners = Account::with(['thirdParties.affiliatePartner', 'data'])
                ->where('account_status', 'Deactivated')
                ->whereHas('thirdParties.affiliatePartner')
                ->get()
                ->map(function ($account) {
                    $tp = $account->thirdParties->first();
                    $partner = $tp->affiliatePartner ?? null;
                    $name = $partner ? $partner->ap_name : 'N/A';
                    $partnerId = $partner->ap_id ?? null;

                    return (object) [
                        'account_id' => $account->account_id,
                        'type' => 'Affiliate Partner',
                        'name' => $name,
                        'deactivated_at' => $account->data->updated_at ?? null,
                        'member_id_for_link' => $partnerId,
                    ];
                });
            $accounts = $accounts->merge($partners);
        }

        if ($search) {
            $accounts = $accounts->filter(function ($item) use ($search) {
                return stripos($item->name, $search) !== false || stripos($item->type, $search) !== false;
            });
        }

        if ($sortBy === 'oldest') {
            $accounts = $accounts->sortBy('deactivated_at');
        } elseif ($sortBy === 'name_asc') {
            $accounts = $accounts->sortBy('name');
        } elseif ($sortBy === 'name_desc') {
            $accounts = $accounts->sortByDesc('name');
        } else {
            $accounts = $accounts->sortByDesc('deactivated_at');
        }

        if ($perPage === 'all') {
            $accounts = $accounts->values();
        } else {
            $currentPage = $request->input('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $total = $accounts->count();
            $items = $accounts->slice($offset, $perPage)->values();
            $accounts = new LengthAwarePaginator($items, $total, $perPage, $currentPage, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        return view('pages.dashboard.system.deactivated-accounts', ['accounts' => $accounts]);
    }

    public function reactivate($accountId)
    {
        $account = Account::find($accountId);

        if ($account) {
            $account->update(['account_status' => 'Active']);

            return redirect()->route('accounts.deactivated')->with('success', 'Account reactivated successfully.');
        }

        return redirect()->route('accounts.deactivated')->with('error', 'Account not found.');
    }

    public function destroy($accountId)
    {
        DB::beginTransaction();
        try {
            $account = Account::with(['members', 'thirdParties', 'data'])->find($accountId);

            if ($account) {
                foreach ($account->members as $member) {
                    Staff::where('member_id', $member->member_id)->delete();
                    $member->delete();
                }

                foreach ($account->thirdParties as $tp) {
                    Sponsor::where('tp_id', $tp->tp_id)->delete();
                    AffiliatePartner::where('tp_id', $tp->tp_id)->delete();
                    $tp->delete();
                }

                if ($account->data) {
                    $account->data->delete();
                }

                $account->delete();
            }

            DB::commit();

            return redirect()->route('accounts.deactivated')->with('success', 'Account permanently deleted.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('accounts.deactivated')->with('error', 'Failed to delete account.');
        }
    }
}
