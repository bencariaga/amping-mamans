<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Storage\Data;
use App\Models\User\AffiliatePartner;
use App\Models\Authentication\Account;
use App\Models\Operation\GuaranteeLetter;
use Exception;

class AffiliatePartnerController extends Controller
{
    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $lastNum = $max ? (int) Str::afterLast($max, '-') : 0;
        return $base . '-' . Str::padLeft($lastNum + 1, 9, '0');
    }

    public function index()
    {
        return response()->json(AffiliatePartner::join('accounts', 'affiliate_partners.account_id', '=', 'accounts.account_id')->join('data', 'accounts.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get());
    }

    public function confirmChanges(Request $request)
    {
        $changes = $request->all();
        DB::beginTransaction();

        try {
            // Handle Creations
            foreach ($changes['create'] as $partnerData) {
                $partnerName = Str::of($partnerData['affiliate_partner_name'])->trim();
                $partnerType = Str::of($partnerData['affiliate_partner_type'])->trim();

                if ($partnerName === '' || $partnerType === '') {
                    continue;
                }

                $dataId = $this->generateNextId('DATA', 'data', 'data_id');
                Data::create([
                    'data_id' => $dataId,
                    'data_status' => 'Unarchived',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $accountId = $this->generateNextId('ACCOUNT', 'accounts', 'account_id');
                Account::create([
                    'account_id' => $accountId,
                    'data_id' => $dataId,
                    'account_status' => 'Active',
                    'registered_at' => now(),
                    'last_deactivated_at' => null,
                    'last_reactivated_at' => null
                ]);

                AffiliatePartner::create([
                    'affiliate_partner_id' => $this->generateNextId('AP', 'affiliate_partners', 'affiliate_partner_id'),
                    'account_id' => $accountId,
                    'affiliate_partner_name' => (string) $partnerName,
                    'affiliate_partner_type' => (string) $partnerType,
                ]);
            }

            // Handle Updates
            foreach ($changes['update'] as $partnerData) {
                $partnerName = Str::of($partnerData['affiliate_partner_name'])->trim();
                $partnerType = Str::of($partnerData['affiliate_partner_type'])->trim();

                if ($partnerName === '' || $partnerType === '') {
                    continue;
                }

                $partner = AffiliatePartner::find($partnerData['affiliate_partner_id']);
                if ($partner) {
                    $partner->update([
                        'affiliate_partner_name' => (string) $partnerName,
                        'affiliate_partner_type' => (string) $partnerType
                    ]);

                    if ($partner->account && $partner->account->data) {
                        $partner->account->data->touch();
                    }
                }
            }

            foreach ($changes['delete'] as $partnerId) {
                $partner = AffiliatePartner::find($partnerId);

                if ($partner) {
                    $guaranteeLetterCount = GuaranteeLetter::where('affiliate_partner_id', $partner->affiliate_partner_id)->count();

                    if ($guaranteeLetterCount > 0) {
                        throw new Exception("Cannot delete Affiliate Partner '{$partner->affiliate_partner_name}' because {$guaranteeLetterCount} guarantee letter(s) are associated with it.");
                    }

                    $accountId = $partner->account_id;
                    $partner->delete();
                    $referencing = DB::table('affiliate_partners')->where('account_id', $accountId)->exists();

                    if (!$referencing) {
                        $account = Account::find($accountId);
                        if ($account) {
                            $dataId = $account->data_id;
                            $account->delete();
                            $data = Data::find($dataId);
                            if ($data) {
                                $data->delete();
                            }
                        }
                    }
                }
            }

            DB::commit();

            $updatedAffiliatePartners = AffiliatePartner::join('accounts', 'affiliate_partners.account_id', '=', 'accounts.account_id')->join('data', 'accounts.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get()->map(function ($partner) {
                return [
                    'id' => $partner->affiliate_partner_id,
                    'name' => $partner->affiliate_partner_name,
                    'type' => $partner->affiliate_partner_type,
                    'status' => 'existing'
                ];
            });

            return response()->json(['success' => true, 'affiliatePartners' => $updatedAffiliatePartners]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

