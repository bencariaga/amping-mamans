<?php

namespace App\Actions\Household;

use Illuminate\Support\Str;

class PrepareHouseholdMembers
{
    public function execute(array $members): array
    {
        foreach ($members as $index => $memberData) {
            if (isset($memberData['monthly_income'])) {
                $members[$index]['monthly_income'] = Str::replace(',', '', $memberData['monthly_income']);
            }
        }

        return $members;
    }
}
