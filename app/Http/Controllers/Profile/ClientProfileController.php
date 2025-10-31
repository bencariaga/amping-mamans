<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Authentication\Account;
use App\Models\Authentication\Occupation;
use App\Models\Operation\Data;
use App\Models\User\Applicant;
use App\Models\User\Client;
use App\Models\User\Contact;
use App\Models\User\Member;
use App\Models\User\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientProfileController extends Controller
{
    public function show(Applicant $applicant)
    {
        $occupations = Occupation::all();
        $applicantData = $this->loadApplicantData($applicant);

        return view('pages.sidebar.profiles.profile.applicants', [
            'applicant' => $applicant,
            'occupations' => $occupations,
            'applicantData' => $applicantData,
        ]);
    }

    public function update(Request $request, Applicant $applicant)
    {
        $contactId = Contact::where('client_id', $applicant->client_id)->where('contact_type', 'Application')->value('contact_id');

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:20'],
            'middle_name' => ['nullable', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:20'],
            'suffix' => ['nullable', 'string', 'in:Sr.,Jr.,II,III,IV,V'],
            'birth_date' => ['required', 'date'],
            'sex' => ['required', 'string', 'in:Male,Female'],
            'civil_status' => ['required', 'string', 'in:Single,Married,Widowed,Separated'],
            'phone_number' => ['required', 'string', 'max:17', Rule::unique('contacts', 'phone_number')->ignore($contactId, 'contact_id')],
            'barangay' => ['nullable', 'string'],
            'occupation_id' => ['nullable', 'string', 'exists:occupations,occupation_id'],
            'custom_occupation' => ['nullable', 'string', 'max:30'],
            'job_status' => ['nullable', 'string', 'in:Retired,Permanent,Contractual,Casual'],
            'monthly_income' => ['required', 'integer', 'min:0', 'max:999999'],
            'house_occup_status' => ['required', 'string', 'in:Owner,Renter,House Sharer'],
            'lot_occup_status' => ['required', 'string', 'in:Owner,Renter,Lot Sharer,Informal Settler'],
            'phic_affiliation' => ['required', 'string', 'in:Affiliated,Unaffiliated'],
            'phic_category' => ['nullable', 'string', 'in:Self-Employed,Sponsored / Indigent,Employed'],
            'patient_number' => ['required', 'integer', 'min:1', 'max:10'],
            'patients.*.last_name' => ['required', 'string', 'max:20'],
            'patients.*.first_name' => ['required', 'string', 'max:20'],
            'patients.*.middle_name' => ['nullable', 'string', 'max:20'],
            'patients.*.suffix' => ['nullable', 'string', 'in:Sr.,Jr.,II,III,IV,V'],
            'patients.*.sex' => ['required', 'string', 'in:Male,Female'],
            'patients.*.age' => ['required', 'integer', 'min:1', 'max:999'],
            'patients.*.patient_category' => ['nullable', 'string', 'in:PWD,Senior'],
            'include_applicant_as_patient' => ['nullable', 'boolean'],
            'applicant_age' => ['required', 'integer', 'min:0'],
            'province' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'municipality' => ['nullable', 'string'],
            'subdivision' => ['nullable', 'string'],
            'purok' => ['nullable', 'string'],
            'sitio' => ['nullable', 'string'],
            'street' => ['nullable', 'string'],
            'phase' => ['nullable', 'string'],
            'block_number' => ['nullable', 'string'],
            'house_number' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $applicant) {
            $client = $applicant->client;
            $member = $client->member;
            $account = $member->account;

            $occId = $validated['occupation_id'] ?? null;

            if (! empty($validated['custom_occupation'])) {
                $d2 = $this->generateNextId('DATA', 'data', 'data_id');

                Data::create([
                    'data_id' => $d2,
                    'data_status' => 'Unarchived',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $occupation = Occupation::create([
                    'occupation_id' => $this->generateNextId('OCCUP', 'occupations', 'occupation_id'),
                    'data_id' => $d2,
                    'occupation' => $validated['custom_occupation'],
                ]);

                $occId = $occupation->occupation_id;
            }

            $fullName = collect([
                $validated['first_name'],
                $validated['middle_name'],
                $validated['last_name'],
                $validated['suffix'],
            ])->filter()->implode(' ');

            $member->update([
                'first_name' => Str::title($validated['first_name']),
                'middle_name' => Str::title($validated['middle_name']) ?: null,
                'last_name' => Str::title($validated['last_name']),
                'suffix' => $validated['suffix'] ?: null,
                'full_name' => Str::title($fullName),
            ]);

            $client->update([
                'occupation_id' => $occId,
                'birthdate' => $validated['birth_date'],
                'age' => $validated['applicant_age'],
                'sex' => $validated['sex'],
                'civil_status' => $validated['civil_status'],
                'monthly_income' => $validated['monthly_income'],
            ]);

            $contact = Contact::where('client_id', $client->client_id)->where('contact_type', 'Application')->first();
            if ($contact) {
                $contact->update([
                    'phone_number' => $this->normalizePhoneNumber($validated['phone_number']),
                ]);
            }

            $phicCategory = $validated['phic_affiliation'] === 'Affiliated' ? ($validated['phic_category'] ?? null) : null;
            $jobStatus = ($validated['job_status'] ?? '') === '' ? null : $validated['job_status'];

            $applicant->update([
                'province' => $validated['province'] ?? 'South Cotabato',
                'city' => $validated['city'] ?? 'General Santos',
                'municipality' => $validated['municipality'] ?? 'N / A',
                'barangay' => ($validated['barangay'] ?? '') === '' ? null : $validated['barangay'],
                'subdivision' => ($validated['subdivision'] ?? '') === '' ? null : $validated['subdivision'],
                'purok' => ($validated['purok'] ?? '') === '' ? null : $validated['purok'],
                'sitio' => ($validated['sitio'] ?? '') === '' ? null : $validated['sitio'],
                'street' => $validated['street'] ?? '',
                'phase' => ($validated['phase'] ?? '') === '' ? null : $validated['phase'],
                'block_number' => ($validated['block_number'] ?? '') === '' ? null : $validated['block_number'],
                'house_number' => ($validated['house_number'] ?? '') === '' ? null : $validated['house_number'],
                'job_status' => $jobStatus,
                'house_occup_status' => $validated['house_occup_status'],
                'lot_occup_status' => $validated['lot_occup_status'],
                'phic_affiliation' => $validated['phic_affiliation'],
                'phic_category' => $phicCategory,
                'is_also_patient' => ($validated['include_applicant_as_patient'] ?? false) ? 'Yes' : 'No',
                'patient_number' => $validated['patient_number'],
            ]);

            $existingPatients = Patient::where('applicant_id', $applicant->applicant_id)->with(['client.member'])->get();

            $applicantClientId = $client->client_id;
            $existingApplicantPatient = $existingPatients->firstWhere('client_id', $applicantClientId);
            $existingOtherPatients = $existingPatients->where('client_id', '!=', $applicantClientId)->values();

            $patientsToKeepClientIds = [];
            $otherPatientIndex = 0;

            foreach ($validated['patients'] as $index => $patientData) {
                $isApplicantPatient = ($validated['include_applicant_as_patient'] ?? false) && $index == 1;

                if ($isApplicantPatient) {
                    if ($existingApplicantPatient) {
                        $existingApplicantPatient->update(['patient_category' => $patientData['patient_category'] ?: null]);
                    } else {
                        Patient::create([
                            'patient_id' => $this->generateNextId('PATIENT', 'patients', 'patient_id'),
                            'client_id' => $applicantClientId,
                            'applicant_id' => $applicant->applicant_id,
                            'patient_category' => $patientData['patient_category'] ?: null,
                        ]);
                    }

                    $patientsToKeepClientIds[] = $applicantClientId;
                } else {
                    $patientToUpdate = $existingOtherPatients->get($otherPatientIndex);

                    if ($patientToUpdate) {
                        $patientClient = $patientToUpdate->client;
                        $patientMember = $patientClient ? $patientClient->member : null;

                        if ($patientMember) {
                            $patientFullName = collect([
                                $patientData['first_name'],
                                $patientData['middle_name'],
                                $patientData['last_name'],
                                $patientData['suffix'],
                            ])->filter()->implode(' ');

                            $patientMember->update([
                                'first_name' => Str::title($patientData['first_name']),
                                'middle_name' => Str::title($patientData['middle_name']) ?: null,
                                'last_name' => Str::title($patientData['last_name']),
                                'suffix' => $patientData['suffix'] ?: null,
                                'full_name' => Str::title($patientFullName),
                            ]);
                        }

                        if ($patientClient) {
                            $patientClient->update([
                                'age' => $patientData['age'],
                                'sex' => $patientData['sex'],
                            ]);
                        }

                        $patientToUpdate->update([
                            'patient_category' => $patientData['patient_category'] ?: null,
                        ]);

                        $patientsToKeepClientIds[] = $patientToUpdate->client_id;
                        $otherPatientIndex++;
                    } else {
                        $pMemberId = $this->generateNextId('MEMBER', 'members', 'member_id');
                        $patientClientId = $this->generateNextId('CLIENT', 'clients', 'client_id');
                        $patientFullName = collect([
                            $patientData['first_name'],
                            $patientData['middle_name'],
                            $patientData['last_name'],
                            $patientData['suffix'],
                        ])->filter()->implode(' ');

                        Member::create([
                            'member_id' => $pMemberId,
                            'account_id' => $account->account_id,
                            'member_type' => 'Patient',
                            'first_name' => Str::title($patientData['first_name']),
                            'middle_name' => Str::title($patientData['middle_name']) ?: null,
                            'last_name' => Str::title($patientData['last_name']),
                            'suffix' => $patientData['suffix'] ?: null,
                            'full_name' => Str::title($patientFullName),
                        ]);

                        Client::create([
                            'client_id' => $patientClientId,
                            'member_id' => $pMemberId,
                            'age' => $patientData['age'],
                            'sex' => $patientData['sex'],
                        ]);

                        Patient::create([
                            'patient_id' => $this->generateNextId('PATIENT', 'patients', 'patient_id'),
                            'client_id' => $patientClientId,
                            'applicant_id' => $applicant->applicant_id,
                            'patient_category' => $patientData['patient_category'] ?: null,
                        ]);

                        $patientsToKeepClientIds[] = $patientClientId;
                    }
                }
            }

            $allExistingPatients = Patient::where('applicant_id', $applicant->applicant_id)
                ->with(['client.member'])
                ->get();

            $patientsToDelete = $allExistingPatients->reject(fn ($p) => collect($patientsToKeepClientIds)->contains($p->client_id));

            foreach ($patientsToDelete as $patientToDelete) {
                $patientClient = $patientToDelete->client;
                $patientMember = $patientClient ? $patientClient->member : null;

                $patientToDelete->delete();

                if ($patientClient && $patientClient->client_id !== $applicantClientId) {
                    $patientClient->delete();
                }

                if ($patientMember && $patientMember->member_id !== $member->member_id) {
                    $patientMember->delete();

                    if ($patientMember->account_id && $patientMember->account_id !== $account->account_id) {
                        $patientAccount = Account::where('account_id', $patientMember->account_id)->first();
                        if ($patientAccount) {
                            $patientAccount->delete();

                            if ($patientAccount->data_id) {
                                Data::where('data_id', $patientAccount->data_id)->delete();
                            }
                        }
                    }
                }
            }
        });

        return redirect()->route('profiles.applicants.show', ['applicant' => $applicant->applicant_id])->with('success', 'Applicant profile has been updated successfully.');
    }

    public function destroy(Request $request, Applicant $applicant)
    {
        $fullName = (string) Str::of("{$applicant->client->member->first_name} {$applicant->client->member->last_name}")->trim();
        $entered = Str::of($request->input('deleteConfirmationText', ''))->trim();

        if (strcasecmp($entered, $fullName) !== 0) {
            session()->flash('error', 'Confirmation text does not match.');

            return back()->withInput();
        }

        DB::transaction(function () use ($applicant) {
            $mainApplicantMemberId = $applicant->client->member->member_id;
            $patientMemberIdsToDelete = [];

            foreach ($applicant->patients as $patient) {
                if ($patient->client && $patient->client->member_id !== $mainApplicantMemberId) {
                    $patientMemberIdsToDelete[] = $patient->client->member_id;
                }

                $patient->delete();
            }

            $applicant->client->contacts()->delete();
            $applicant->delete();
            $dataId = $applicant->client->member->account->data_id;
            $accountId = $applicant->client->member->account_id;
            $memberId = $applicant->client->member_id;
            $clientId = $applicant->client_id;

            $clientToDelete = Client::find($clientId);
            if ($clientToDelete) {
                $clientToDelete->delete();
            }

            foreach ($patientMemberIdsToDelete as $memberIdToDel) {
                $clientToDelete = Client::where('member_id', $memberIdToDel)->first();

                if ($clientToDelete) {
                    $clientToDelete->delete();
                }

                $memberToDelete = Member::find($memberIdToDel);
                if ($memberToDelete) {
                    $memberToDelete->delete();
                }
            }

            $memberToDelete = Member::find($memberId);
            if ($memberToDelete) {
                $memberToDelete->delete();
            }

            $accountToDelete = Account::find($accountId);
            if ($accountToDelete) {
                $accountToDelete->delete();
            }

            $dataToDelete = Data::find($dataId);
            if ($dataToDelete) {
                $dataToDelete->delete();
            }
        });

        session()->flash('success', 'Applicant and all associated data have been successfully deleted.');

        return redirect()->route('profiles.applicants.list');
    }

    private function loadApplicantData(Applicant $applicant): array
    {
        $client = $applicant->client;
        $member = $client->member;
        $contact = Contact::where('client_id', $client->client_id)->where('contact_type', 'Application')->first();

        $patients = Patient::where('applicant_id', $applicant->applicant_id)->get();
        $patientsData = [];

        // Separate applicant-as-patient from other patients
        $applicantPatient = $patients->firstWhere('client_id', $client->client_id);
        $otherPatients = $patients->where('client_id', '!=', $client->client_id)->values();

        $currentIndex = 1;

        // If applicant is also a patient, they should be at index 1
        if ($applicantPatient) {
            $patientClient = Client::where('client_id', $applicantPatient->client_id)->first();
            $patientMember = $patientClient ? Member::where('member_id', $patientClient->member_id)->first() : null;

            $patientsData[$currentIndex] = [
                'last_name' => $patientMember->last_name ?? '',
                'first_name' => $patientMember->first_name ?? '',
                'middle_name' => $patientMember->middle_name ?? '',
                'suffix' => $patientMember->suffix ?? '',
                'sex' => $patientClient->sex ?? '',
                'age' => (int) ($patientClient->age ?? 0),
                'patient_category' => $applicantPatient->patient_category ?? '',
                'client_id' => $applicantPatient->client_id,
            ];
            $currentIndex++;
        }

        // Add other patients
        foreach ($otherPatients as $patient) {
            $patientClient = Client::where('client_id', $patient->client_id)->first();
            $patientMember = $patientClient ? Member::where('member_id', $patientClient->member_id)->first() : null;

            $patientsData[$currentIndex] = [
                'last_name' => $patientMember->last_name ?? '',
                'first_name' => $patientMember->first_name ?? '',
                'middle_name' => $patientMember->middle_name ?? '',
                'suffix' => $patientMember->suffix ?? '',
                'sex' => $patientClient->sex ?? '',
                'age' => (int) ($patientClient->age ?? 0),
                'patient_category' => $patient->patient_category ?? '',
                'client_id' => $patient->client_id,
            ];
            $currentIndex++;
        }

        if (empty($patientsData)) {
            $patientsData = [1 => ['last_name' => '', 'first_name' => '', 'middle_name' => '', 'suffix' => '', 'sex' => '', 'age' => '', 'patient_category' => '', 'client_id' => null]];
        }

        return [
            'first_name' => $member->first_name,
            'middle_name' => $member->middle_name,
            'last_name' => $member->last_name,
            'suffix' => $member->suffix ?? '',
            'birth_date' => $client->birthdate,
            'sex' => $client->sex,
            'civil_status' => $client->civil_status,
            'phone_number' => $contact->phone_number ?? '',
            'province' => $applicant->province ?? 'South Cotabato',
            'city' => $applicant->city ?? 'General Santos',
            'municipality' => $applicant->municipality ?? 'N / A',
            'barangay' => $applicant->barangay ?? '',
            'subdivision' => $applicant->subdivision ?? '',
            'purok' => $applicant->purok ?? '',
            'sitio' => $applicant->sitio ?? '',
            'street' => $applicant->street ?? '',
            'phase' => $applicant->phase ?? '',
            'block_number' => $applicant->block_number ?? '',
            'house_number' => $applicant->house_number ?? '',
            'occupation_id' => $client->occupation_id,
            'custom_occupation' => '',
            'job_status' => $applicant->job_status ?? '',
            'monthly_income' => isset($client->monthly_income) ? (int) $client->monthly_income : 0,
            'house_occup_status' => $applicant->house_occup_status ?? '',
            'lot_occup_status' => $applicant->lot_occup_status ?? '',
            'phic_affiliation' => $applicant->phic_affiliation ?? '',
            'phic_category' => $applicant->phic_category ?? null,
            'patient_number' => (int) $applicant->patient_number,
            'include_applicant_as_patient' => $applicant->is_also_patient === 'Yes',
            'applicant_age' => (int) $client->age,
            'patients' => $patientsData,
        ];
    }

    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $last = $max ? (int) Str::afterLast($max, '-') : 0;

        return $base.'-'.Str::padLeft($last + 1, 9, '0');
    }

    private function normalizePhoneNumber(string $value): string
    {
        $raw = Str::of($value)->trim();
        $clean = Str::of($raw)->replaceMatches('/[^0-9+]/', '')->toString();

        if (Str::startsWith($clean, '+')) {
            $clean = Str::substr($clean, 1);
        }

        $clean = Str::of($clean)->replaceMatches('/[^0-9]/', '')->toString();

        if (Str::startsWith($clean, '63')) {
            $clean = '0'.Str::substr($clean, 2);
        } elseif (Str::startsWith($clean, '9')) {
            $clean = '0'.$clean;
        } elseif (! Str::startsWith($clean, '0')) {
            $clean = '0'.$clean;
        }

        if (strlen($clean) >= 11) {
            $part1 = Str::substr($clean, 0, 4);
            $part2 = Str::substr($clean, 4, 3);
            $part3 = Str::substr($clean, 7, 4);
            $formatted = $part1;

            if ($part2 !== false && $part2 !== '') {
                $formatted .= '-'.$part2;
            }

            if ($part3 !== false && $part3 !== '') {
                $formatted .= '-'.$part3;
            }

            return $formatted;
        }

        if (strlen($clean) > 4) {
            $part1 = Str::substr($clean, 0, 4);
            $part2 = Str::substr($clean, 4);

            return $part1.($part2 ? '-'.$part2 : '');
        }

        return $clean;
    }
}
