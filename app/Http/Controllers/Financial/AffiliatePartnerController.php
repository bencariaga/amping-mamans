<?php

namespace App\Http\Controllers\Financial;

use App\Actions\Financial\CreateAffiliatePartner;
use App\Actions\Financial\DeleteAffiliatePartner;
use App\Actions\Financial\UpdateAffiliatePartner;
use App\Http\Controllers\Controller;
use App\Models\User\AffiliatePartner;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AffiliatePartnerController extends Controller
{

    public function index()
    {
        return response()->json(AffiliatePartner::join('accounts', 'affiliate_partners.account_id', '=', 'accounts.account_id')->join('data', 'accounts.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get());
    }

    public function confirmChanges(Request $request, CreateAffiliatePartner $createAffiliatePartner, UpdateAffiliatePartner $updateAffiliatePartner, DeleteAffiliatePartner $deleteAffiliatePartner)
    {
        $changes = $request->all();
        DB::beginTransaction();

        try {
            foreach ($changes['create'] as $partnerData) {
                $partnerName = Str::of($partnerData['affiliate_partner_name'])->trim();
                $partnerType = Str::of($partnerData['affiliate_partner_type'])->trim();

                if ($partnerName === '' || $partnerType === '') {
                    continue;
                }

                $createAffiliatePartner->execute((string) $partnerName, (string) $partnerType);
            }

            foreach ($changes['update'] as $partnerData) {
                $partnerName = Str::of($partnerData['affiliate_partner_name'])->trim();
                $partnerType = Str::of($partnerData['affiliate_partner_type'])->trim();

                if ($partnerName === '' || $partnerType === '') {
                    continue;
                }

                $updateAffiliatePartner->execute($partnerData['affiliate_partner_id'], (string) $partnerName, (string) $partnerType);
            }

            foreach ($changes['delete'] as $partnerId) {
                $deleteAffiliatePartner->execute($partnerId);
            }

            DB::commit();

            $updatedAffiliatePartners = AffiliatePartner::join('accounts', 'affiliate_partners.account_id', '=', 'accounts.account_id')->join('data', 'accounts.data_id', '=', 'data.data_id')->orderBy('data.updated_at', 'desc')->get()->map(function ($partner) {
                return [
                    'id' => $partner->affiliate_partner_id,
                    'name' => $partner->affiliate_partner_name,
                    'type' => $partner->affiliate_partner_type,
                    'status' => 'existing',
                ];
            });

            return response()->json(['success' => true, 'affiliatePartners' => $updatedAffiliatePartners]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
