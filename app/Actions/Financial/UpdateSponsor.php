<?php

namespace App\Actions\Financial;

use App\Models\User\Sponsor;
use Illuminate\Support\Facades\DB;

class UpdateSponsor
{
    public function execute(string $sponsorId, array $sponsorData): Sponsor
    {
        return DB::transaction(function () use ($sponsorId, $sponsorData) {
            $sponsor = Sponsor::with('member')->findOrFail($sponsorId);

            if ($sponsor->member) {
                $sponsor->member->update([
                    'first_name' => $sponsorData['first_name'] ?? $sponsor->member->first_name,
                    'middle_name' => $sponsorData['middle_name'] ?? $sponsor->member->middle_name,
                    'last_name' => $sponsorData['last_name'] ?? $sponsor->member->last_name,
                    'suffix' => $sponsorData['suffix'] ?? $sponsor->member->suffix,
                ]);
            }

            $sponsor->update([
                'sponsor_type' => $sponsorData['sponsor_type'] ?? $sponsor->sponsor_type,
                'designation' => $sponsorData['designation'] ?? $sponsor->designation,
                'organization_name' => $sponsorData['organization_name'] ?? $sponsor->organization_name,
            ]);

            return $sponsor->fresh(['member']);
        });
    }
}
