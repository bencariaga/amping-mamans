<?php

namespace App\Livewire\Client;

use App\Models\Authentication\Account;
use App\Models\Authentication\Occupation;
use App\Models\Storage\Data;
use App\Models\User\Applicant;
use App\Models\User\Client;
use App\Models\User\Contact;
use App\Models\User\Member;
use App\Models\User\Patient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ClientProfile extends Component
{
    public string $applicantId;

    public string $first_name = '';

    public ?string $middle_name = null;

    public string $last_name = '';

    public string $suffix = '';

    public string $birth_date = '';

    public string $sex = '';

    public string $civil_status = '';

    public string $phone_number = '';

    public string $province = 'South Cotabato';

    public string $city = 'General Santos';

    public string $municipality = 'N / A';

    public string $barangay = '';

    public string $subdivision = '';

    public string $purok = '';

    public string $sitio = '';

    public string $street = '';

    public string $phase = '';

    public string $block_number = '';

    public string $house_number = '';

    public ?string $occupation_id = null;

    public string $custom_occupation = '';

    public string $job_status = '';

    public int $monthly_income = 0;

    public string $house_occup_status = '';

    public string $lot_occup_status = '';

    public string $phic_affiliation = '';

    public ?string $phic_category = null;

    public int $patient_number = 1;

    public array $patients = [];

    public bool $include_applicant_as_patient = false;

    public int $applicant_age = 0;

    public ?string $client_id = null;

    public ?string $member_id = null;

    public ?string $account_id = null;

    public ?string $contact_id = null;

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:20'],
            'middle_name' => ['nullable', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:20'],
            'suffix' => ['nullable', 'string', 'in:Sr.,Jr.,II,III,IV,V'],
            'birth_date' => ['required', 'date'],
            'sex' => ['required', 'string', 'in:Male,Female'],
            'civil_status' => ['required', 'string', 'in:Single,Married,Widowed,Separated'],
            'phone_number' => ['required', 'string', 'max:17', Rule::unique('contacts', 'phone_number')->ignore($this->contact_id, 'contact_id')],
            'barangay' => ['nullable', 'string'],
            'occupation_id' => ['nullable', 'string', 'exists:occupations,occupation_id'],
            'custom_occupation' => ['nullable', 'string', 'max:30'],
            'job_status' => ['nullable', 'string', 'in:Permanent,Contractual,Casual'],
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
            'include_applicant_as_patient' => ['boolean'],
        ];
    }

    private function syncApplicantData(): void
    {
        if ($this->include_applicant_as_patient) {
            $this->patients[1] = [
                'last_name' => $this->last_name,
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'suffix' => $this->suffix,
                'sex' => $this->sex,
                'age' => $this->applicant_age,
                'patient_category' => $this->patients[1]['patient_category'] ?? '',
                'client_id' => $this->client_id,
            ];
        }
    }

    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $last = $max ? (int) Str::afterLast($max, '-') : 0;

        return $base.'-'.Str::padLeft($last + 1, 9, '0');
    }

    public function mount($applicantId)
    {
        $this->applicantId = $applicantId;
        $applicant = Applicant::where('applicant_id', $applicantId)->first();

        if ($applicant) {
            $this->client_id = $applicant->client_id;
            $client = Client::where('client_id', $applicant->client_id)->first();

            if ($client) {
                $this->member_id = $client->member_id;
                $this->birth_date = $client->birthdate;
                $this->sex = $client->sex;
                $this->civil_status = $client->civil_status;
                $this->monthly_income = isset($client->monthly_income) ? (int) $client->monthly_income : 0;
                $this->occupation_id = $client->occupation_id;
                $this->applicant_age = (int) $client->age;
            }

            $member = Member::where('member_id', $this->member_id)->first();

            if ($member) {
                $this->first_name = $member->first_name;
                $this->middle_name = $member->middle_name;
                $this->last_name = $member->last_name;
                $this->suffix = $member->suffix ?? '';
                $this->account_id = $member->account_id;
            }

            $contact = Contact::where('client_id', $this->client_id)->where('contact_type', 'Application')->first();

            if ($contact) {
                $this->phone_number = $contact->phone_number;
                $this->contact_id = $contact->contact_id;
            }

            $this->province = $applicant->province ?? $this->province;
            $this->city = $applicant->city ?? $this->city;
            $this->municipality = $applicant->municipality ?? $this->municipality;
            $this->barangay = $applicant->barangay ?? $this->barangay;
            $this->subdivision = $applicant->subdivision ?? $this->subdivision;
            $this->purok = $applicant->purok ?? $this->purok;
            $this->sitio = $applicant->sitio ?? $this->sitio;
            $this->street = $applicant->street ?? $this->street;
            $this->phase = $applicant->phase ?? $this->phase;
            $this->block_number = $applicant->block_number ?? $this->block_number;
            $this->house_number = $applicant->house_number ?? $this->house_number;
            $this->job_status = $applicant->job_status ?? $this->job_status;
            $this->house_occup_status = $applicant->house_occup_status ?? $this->house_occup_status;
            $this->lot_occup_status = $applicant->lot_occup_status ?? $this->lot_occup_status;
            $this->phic_affiliation = $applicant->phic_affiliation ?? $this->phic_affiliation;
            $this->phic_category = $applicant->phic_category ?? $this->phic_category;
            $this->patient_number = $applicant->patient_number;
            $this->include_applicant_as_patient = $applicant->is_also_patient === 'Yes';
        }

        $patients = Patient::where('applicant_id', $this->applicantId)->get();
        $this->patients = [];

        if ($patients->count() > 0) {
            foreach ($patients as $index => $patient) {
                $client = Client::where('client_id', $patient?->client_id)->first();
                $member = Member::where('member_id', $client?->member_id)->first();

                $this->patients[$index + 1] = [
                    'last_name' => $member->last_name ?? '',
                    'first_name' => $member->first_name ?? '',
                    'middle_name' => $member->middle_name ?? '',
                    'suffix' => $member->suffix ?? '',
                    'sex' => $client->sex ?? '',
                    'age' => (int) ($client->age ?? 0),
                    'patient_category' => $patient->patient_category ?? '',
                    'client_id' => $patient->client_id,
                ];
            }
        } else {
            $this->patients = [1 => ['last_name' => '', 'first_name' => '', 'middle_name' => '', 'suffix' => '', 'sex' => '', 'age' => '', 'patient_category' => '', 'client_id' => null]];
        }

        if ($this->include_applicant_as_patient) {
            $this->syncApplicantData();
        }

        $this->dispatch('update-ui-elements');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        if (collect(['first_name', 'middle_name', 'last_name', 'suffix', 'birth_date', 'sex'])->contains($propertyName)) {
            $this->syncApplicantData();
        }
    }

    public function updatedIncludeApplicantAsPatient(bool $value)
    {
        if ($value) {
            $this->syncApplicantData();
        } else {
            $existingPatient = Patient::where('applicant_id', $this->applicantId)->where('client_id', $this->client_id)->first();
            $clientToReset = $existingPatient ? Client::where('client_id', $existingPatient->client_id)->first() : null;
            $memberToReset = $clientToReset ? Member::where('member_id', $clientToReset->member_id)->first() : null;

            $this->patients[1] = [
                'last_name' => $memberToReset->last_name ?? '',
                'first_name' => $memberToReset->first_name ?? '',
                'middle_name' => $memberToReset->middle_name ?? null,
                'suffix' => $memberToReset->suffix ?? null,
                'sex' => $clientToReset->sex ?? '',
                'age' => (int) ($clientToReset->age ?? 0),
                'patient_category' => $existingPatient->patient_category ?? '',
                'client_id' => $this->client_id,
            ];
        }
    }

    public function updatedPhicAffiliation()
    {
        if ($this->phic_affiliation !== 'Affiliated') {
            $this->phic_category = null;
        }
    }

    public function updatedPatientNumber()
    {
        $patientNumber = (int) $this->patient_number;
        $currentCount = collect($this->patients)->count();

        if ($patientNumber > $currentCount) {
            for ($i = $currentCount + 1; $i <= $patientNumber; $i++) {
                $this->patients[$i] = ['last_name' => '', 'first_name' => '', 'middle_name' => '', 'suffix' => '', 'sex' => '', 'age' => '', 'patient_category' => '', 'client_id' => null];
            }
        } elseif ($patientNumber < $currentCount) {
            $this->patients = collect($this->patients)->slice(0, $patientNumber)->all();
        }

        if ($this->include_applicant_as_patient) {
            $this->syncApplicantData();
        }
    }

    public function setSuffix($value)
    {
        $this->suffix = $value;
        $this->syncApplicantData();
    }

    public function setSex($value)
    {
        $this->sex = $value;
        $this->syncApplicantData();
    }

    public function setCivilStatus($value)
    {
        $this->civil_status = $value;
    }

    public function setBarangay($value)
    {
        $this->barangay = $value;
    }

    public function setHouseOccupStatus($value)
    {
        $this->house_occup_status = $value;
    }

    public function setLotOccupStatus($value)
    {
        $this->lot_occup_status = $value;
    }

    public function setJobStatus($status)
    {
        $this->job_status = $status;
    }

    public function setOccupation(?string $value)
    {
        $this->occupation_id = $value;
        $this->custom_occupation = $value === null && ! $this->custom_occupation ? '' : $this->custom_occupation;
    }

    public function setPhicAffiliation($value)
    {
        $this->phic_affiliation = $value;

        if ($this->phic_affiliation !== 'Affiliated') {
            $this->phic_category = null;
        }
    }

    public function setPhicCategory($value)
    {
        $this->phic_category = $value;
    }

    public function setPatientSuffix($index, $value)
    {
        $this->patients[$index]['suffix'] = $value;
    }

    public function setPatientSex($index, $value)
    {
        $this->patients[$index]['sex'] = $value;
    }

    public function setPatientCategory($index, $value)
    {
        $this->patients[$index]['patient_category'] = $value;
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

    public function update()
    {
        $this->validate();

        $cleanedIncome = Str::of($this->monthly_income)->replaceMatches('/\\D/', '')->toString();

        if ($cleanedIncome === '') {
            $this->monthly_income = 0;
        } else {
            $this->monthly_income = (int) $cleanedIncome;
        }

        DB::transaction(function () {
            $occId = $this->occupation_id;

            if ($this->custom_occupation) {
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
                    'occupation' => $this->custom_occupation,
                ]);

                $occId = $occupation->occupation_id;
            }

            if ($this->member_id) {
                $fullName = collect([$this->first_name, $this->middle_name, $this->last_name, $this->suffix])->filter()->implode(' ');

                Member::where('member_id', $this->member_id)->update([
                    'first_name' => Str::title($this->first_name),
                    'middle_name' => Str::title($this->middle_name) ?: null,
                    'last_name' => Str::title($this->last_name),
                    'suffix' => $this->suffix ?: null,
                    'full_name' => Str::title($fullName),
                ]);
            }

            if ($this->client_id) {
                $client = Client::where('client_id', $this->client_id)->first();

                if ($client) {
                    $client->update([
                        'occupation_id' => $occId,
                        'birthdate' => $this->birth_date,
                        'age' => $this->applicant_age,
                        'sex' => $this->sex,
                        'civil_status' => $this->civil_status,
                        'monthly_income' => $this->monthly_income,
                    ]);
                }
            }

            if ($this->contact_id) {
                Contact::where('contact_id', $this->contact_id)->update([
                    'phone_number' => $this->normalizePhoneNumber($this->phone_number),
                ]);
            }

            $phicCategory = $this->phic_affiliation === 'Affiliated' ? $this->phic_category : null;
            $jobStatus = $this->job_status === '' ? null : $this->job_status;

            Applicant::where('applicant_id', $this->applicantId)->update([
                'province' => $this->province,
                'city' => $this->city,
                'municipality' => $this->municipality,
                'barangay' => $this->barangay === '' ? null : $this->barangay,
                'subdivision' => $this->subdivision === '' ? null : $this->subdivision,
                'purok' => $this->purok === '' ? null : $this->purok,
                'sitio' => $this->sitio === '' ? null : $this->sitio,
                'street' => $this->street,
                'phase' => $this->phase === '' ? null : $this->phase,
                'block_number' => $this->block_number === '' ? null : $this->block_number,
                'house_number' => $this->house_number === '' ? null : $this->house_number,
                'job_status' => $jobStatus,
                'house_occup_status' => $this->house_occup_status,
                'lot_occup_status' => $this->lot_occup_status,
                'phic_affiliation' => $this->phic_affiliation,
                'phic_category' => $phicCategory,
                'is_also_patient' => $this->include_applicant_as_patient ? 'Yes' : 'No',
                'patient_number' => $this->patient_number,
            ]);

            $existingPatients = Patient::where('applicant_id', $this->applicantId)->get()->keyBy('client_id');
            $applicantClientIds = $this->include_applicant_as_patient ? [$this->client_id] : [];
            $patientsToKeepClientIds = $applicantClientIds;

            foreach ($this->patients as $index => $patientData) {
                $isApplicantPatient = $this->include_applicant_as_patient && $index == 1;

                if ($isApplicantPatient) {
                    $patientClientId = $this->client_id;
                    $patientToUpdate = $existingPatients->get($patientClientId);
                } else {
                    $patientClientId = $patientData['client_id'] ?? null;
                    $patientToUpdate = $existingPatients->get($patientClientId);
                }

                if ($patientClientId) {
                    $patientsToKeepClientIds[] = $patientClientId;
                }

                if ($patientToUpdate) {
                    $patientClient = Client::where('client_id', $patientToUpdate->client_id)->first();
                    $patientMember = $patientClient ? Member::where('member_id', $patientClient->member_id)->first() : null;

                    if ($isApplicantPatient) {
                        $patientToUpdate->update(['patient_category' => $patientData['patient_category'] ?: null]);

                        continue;
                    }

                    if ($patientMember) {
                        $patientFullName = collect([$patientData['first_name'], $patientData['middle_name'], $patientData['last_name'], $patientData['suffix']])->filter()->implode(' ');

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
                } elseif (! $isApplicantPatient && (! empty($patientData['first_name']) && ! empty($patientData['last_name']))) {
                    $patientMemberId = $this->generateNextId('MEMBER', 'members', 'member_id');
                    $patientClientId = $this->generateNextId('CLIENT', 'clients', 'client_id');
                    $patientsToKeepClientIds[] = $patientClientId;

                    Data::create(['data_id' => $this->generateNextId('DATA', 'data', 'data_id')]);

                    $patientFullName = collect([$patientData['first_name'], $patientData['middle_name'], $patientData['last_name'], $patientData['suffix']])->filter()->implode(' ');

                    Member::create([
                        'member_id' => $patientMemberId,
                        'account_id' => $this->account_id,
                        'member_type' => 'Patient',
                        'first_name' => Str::title($patientData['first_name']),
                        'middle_name' => Str::title($patientData['middle_name']) ?: null,
                        'last_name' => Str::title($patientData['last_name']),
                        'suffix' => $patientData['suffix'] ?: null,
                        'full_name' => Str::title($patientFullName),
                    ]);

                    Client::create([
                        'client_id' => $patientClientId,
                        'member_id' => $patientMemberId,
                        'age' => $patientData['age'],
                        'sex' => $patientData['sex'],
                    ]);

                    Patient::create([
                        'patient_id' => $this->generateNextId('PATIENT', 'patients', 'patient_id'),
                        'client_id' => $patientClientId,
                        'applicant_id' => $this->applicantId,
                        'patient_category' => $patientData['patient_category'] ?: null,
                    ]);

                    $this->patients[$index]['client_id'] = $patientClientId;
                }
            }

            $patientsToDelete = $existingPatients->reject(fn ($p) => collect($patientsToKeepClientIds)->contains($p->client_id));

            foreach ($patientsToDelete as $patientToDelete) {
                $client = Client::where('client_id', $patientToDelete->client_id)->first();
                $member = $client ? Member::where('member_id', $client->member_id)->first() : null;
                $patientToDelete->delete();

                if ($client) {
                    $client->delete();
                }

                if ($member) {
                    $member->delete();

                    if ($member->account_id) {
                        $account = Account::where('account_id', $member->account_id)->first();
                        Account::where('account_id', $member->account_id)->delete();

                        if ($account?->data_id) {
                            Data::where('data_id', $account->data_id)->delete();
                        }
                    }
                }
            }
        });

        session()->flash('success', 'Applicant profile has been updated successfully.');
        $this->dispatch('update-ui-elements');
        $this->redirectRoute('profiles.applicants.show', ['applicant' => $this->applicantId]);
    }

    public function deleteAccount()
    {
        if ($this->account_id) {
            Account::where('account_id', $this->account_id)->update(['account_status' => 'Deactivated', 'last_deactivated_at' => now()]);
        }

        session()->flash('success', 'Account has been deactivated.');
        $this->redirectRoute('profiles.applicants.list');
    }

    public function render()
    {
        $occupations = Occupation::all();

        return view('livewire.client.client-profile', ['occupations' => $occupations]);
    }
}
