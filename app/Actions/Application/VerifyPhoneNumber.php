<?php

namespace App\Actions\Application;

use App\Models\User\Applicant;
use Illuminate\Support\Str;

class VerifyPhoneNumber
{
    public function execute(string $phoneNumber, array $clientCandidates = []): ?Applicant
    {
        $normalizedCandidates = $this->buildPhoneCandidates($phoneNumber, $clientCandidates);

        if (empty($normalizedCandidates)) {
            return null;
        }

        return Applicant::whereHas('client.contacts', function ($query) use ($normalizedCandidates) {
            $query->where('contact_type', 'Application')->whereIn('contact_number', $normalizedCandidates);
        })->with(['client.member', 'patients.client.member'])->first();
    }

    private function buildPhoneCandidates(string $inputPhone, array $clientCandidates = []): array
    {
        $candidates = collect();

        if ($inputPhone) {
            $raw = Str::of($inputPhone)->trim()->toString();
            $digits = Str::of($raw)->replaceMatches('/\D+/', '')->toString();
            $candidates->push($raw);
            $candidates->push($digits);

            if (Str::startsWith($digits, '63') && Str::length($digits) >= 12) {
                $local = '0' . Str::substr($digits, 2);
                $candidates->push($local);
                $candidates->push(Str::substr($local, 0, 4) . '-' . Str::substr($local, 4, 3) . '-' . Str::substr($local, 7));
                $candidates->push('+' . $digits);
            } elseif (Str::startsWith($digits, '0') && Str::length($digits) === 11) {
                $candidates->push(Str::substr($digits, 0, 4) . '-' . Str::substr($digits, 4, 3) . '-' . Str::substr($digits, 7));
                $candidates->push('+63' . Str::substr($digits, 1));
            } elseif (Str::startsWith($digits, '9') && Str::length($digits) === 10) {
                $local = '0' . $digits;
                $candidates->push($local);
                $candidates->push(Str::substr($local, 0, 4) . '-' . Str::substr($local, 4, 3) . '-' . Str::substr($local, 7));
                $candidates->push('+63' . Str::substr($local, 1));
            }
        }

        collect($clientCandidates)->each(function ($cand) use ($candidates) {
            $c = Str::of($cand)->trim()->toString();

            if ($c) {
                $candidates->push($c);
                $d = Str::of($c)->replaceMatches('/\D+/', '')->toString();

                if ($d) {
                    $candidates->push($d);
                }
            }
        });

        return $candidates->filter()->unique()->values()->all();
    }
}
