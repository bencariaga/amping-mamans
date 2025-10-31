<?php

namespace App\Policies;

use App\Models\User\Applicant;
use App\Models\User\Patient;
use Carbon\Carbon;

class ReapplicationPolicy
{
    private const REAPPLICATION_PERIOD_MONTHS = 6;

    public function canReapply(Applicant $applicant, Patient $patient): bool
    {
        $lastApplication = $patient->applications()
            ->whereIn('application_status', ['Approved', 'Completed'])
            ->orderBy('applied_at', 'desc')
            ->first();

        if (!$lastApplication) {
            return true;
        }

        $reapplyDate = Carbon::parse($lastApplication->reapply_at);
        $now = Carbon::now();

        return $now->greaterThanOrEqualTo($reapplyDate);
    }

    public function getReapplicationDate(?string $lastApplicationDate = null): Carbon
    {
        if (!$lastApplicationDate) {
            return Carbon::now();
        }

        return Carbon::parse($lastApplicationDate)->addMonths(self::REAPPLICATION_PERIOD_MONTHS);
    }

    public function getDaysUntilReapplication(Patient $patient): ?int
    {
        $lastApplication = $patient->applications()
            ->whereIn('application_status', ['Approved', 'Completed'])
            ->orderBy('applied_at', 'desc')
            ->first();

        if (!$lastApplication) {
            return null;
        }

        $reapplyDate = Carbon::parse($lastApplication->reapply_at);
        $now = Carbon::now();

        if ($now->greaterThanOrEqualTo($reapplyDate)) {
            return 0;
        }

        return $now->diffInDays($reapplyDate);
    }
}
