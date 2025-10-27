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
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class ClientRegistration extends Component
{
    public string $first_name = '';

    public ?string $middle_name = '';

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

    protected array $rules = [
        'first_name' => ['required', 'string', 'max:20'],
        'middle_name' => ['nullable', 'string', 'max:20'],
        'last_name' => ['required', 'string', 'max:20'],
        'suffix' => ['nullable', 'string', 'in:Sr.,Jr.,II,III,IV,V'],
        'birth_date' => ['required', 'date'],
        'sex' => ['required', 'string', 'in:Male,Female'],
        'civil_status' => ['required', 'string', 'in:Single,Married,Widowed,Separated'],
        'phone_number' => ['required', 'string', 'max:17', 'unique:contacts,phone_number'],
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

    public function mount()
    {
        $this->job_status = '';
        $this->monthly_income = 0;
        $this->patient_number = 1;
        $this->patients = [1 => ['last_name' => '', 'first_name' => '', 'middle_name' => '', 'suffix' => '', 'sex' => '', 'age' => '', 'patient_category' => '']];
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
            $this->patients[1] = ['last_name' => '', 'first_name' => '', 'middle_name' => '', 'suffix' => '', 'sex' => '', 'age' => '', 'patient_category' => ''];
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
                $this->patients[$i] = ['last_name' => '', 'first_name' => '', 'middle_name' => '', 'suffix' => '', 'sex' => '', 'age' => '', 'patient_category' => ''];
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

    public function save()
    {
        $this->validate();

        $firstErrorField = $this->findFirstErrorField();
        if ($firstErrorField) {
            $this->dispatch('scrollToElement', elementId: $firstErrorField);

            return;
        }

        DB::transaction(function () {
            $dataId = $this->generateNextId('DATA', 'data', 'data_id');
            $acctId = $this->generateNextId('ACCOUNT', 'accounts', 'account_id');
            $memberId = $this->generateNextId('MEMBER', 'members', 'member_id');
            $clientId = $this->generateNextId('CLIENT', 'clients', 'client_id');
            $contactId = $this->generateNextId('CONTACT', 'contacts', 'contact_id');
            $applicantId = $this->generateNextId('APPLICANT', 'applicants', 'applicant_id');

            Data::create([
                'data_id' => $dataId,
                'data_status' => 'Unarchived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Account::create([
                'account_id' => $acctId,
                'data_id' => $dataId,
                'account_status' => 'Active',
                'registered_at' => now(),
            ]);

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

            $fullName = collect([$this->first_name, $this->middle_name, $this->last_name, $this->suffix])->filter()->implode(' ');

            Member::create([
                'member_id' => $memberId,
                'account_id' => $acctId,
                'first_name' => Str::title($this->first_name),
                'middle_name' => Str::title($this->middle_name) ?: null,
                'last_name' => Str::title($this->last_name),
                'suffix' => $this->suffix ?: null,
                'full_name' => Str::title($fullName),
            ]);

            Client::create([
                'client_id' => $clientId,
                'member_id' => $memberId,
                'occupation_id' => $occId,
                'birthdate' => $this->birth_date,
                'age' => $this->applicant_age,
                'sex' => $this->sex,
                'civil_status' => $this->civil_status,
                'monthly_income' => is_numeric($this->monthly_income) ? (float) $this->monthly_income : 0,
            ]);

            $formattedPhone = $this->normalizePhoneNumber($this->phone_number);

            Contact::create([
                'contact_id' => $contactId,
                'client_id' => $clientId,
                'contact_type' => 'Application',
                'phone_number' => $formattedPhone,
            ]);

            $phicCategory = $this->phic_affiliation === 'Affiliated' ? $this->phic_category : null;
            $jobStatus = $this->job_status === '' ? null : $this->job_status;

            Applicant::create([
                'applicant_id' => $applicantId,
                'client_id' => $clientId,
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

            foreach ($this->patients as $index => $patientData) {
                if ($this->include_applicant_as_patient && $index == 1) {
                    $patientClientId = $clientId;
                } else {
                    $pMemberId = $this->generateNextId('MEMBER', 'members', 'member_id');
                    $patientClientId = $this->generateNextId('CLIENT', 'clients', 'client_id');
                    $patientFullName = collect([$patientData['first_name'], $patientData['middle_name'], $patientData['last_name'], $patientData['suffix']])->filter()->implode(' ');

                    Member::create([
                        'member_id' => $pMemberId,
                        'account_id' => $acctId,
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
                }

                Patient::create([
                    'patient_id' => $this->generateNextId('PATIENT', 'patients', 'patient_id'),
                    'client_id' => $patientClientId,
                    'applicant_id' => $applicantId,
                    'patient_category' => $patientData['patient_category'] ?: null,
                ]);
            }
        });

        session()->flash('success', 'Applicant has been added successfully.');
        $this->reset();
        $this->redirectRoute('profiles.applicants.list');
    }

    private function findFirstErrorField(): ?string
    {
        $errorFields = Collection::make($this->getErrorBag()->toArray())->keys()->all();

        $fieldToElementMap = [
            'first_name' => 'applicantFirstNameInput',
            'last_name' => 'applicantLastNameInput',
            'birth_date' => 'applicantBirthdateInput',
            'sex' => 'applicantSexDropdownBtn',
            'civil_status' => 'applicantCivilStatusDropdownBtn',
            'phone_number' => 'applicantPhoneNumberInput',
            'house_occup_status' => 'applicantHouseStatusDropdownBtn',
            'lot_occup_status' => 'applicantLotStatusDropdownBtn',
            'phic_affiliation' => 'applicantPhicAffiliationDropdownBtn',
            'patient_number' => 'patientNumberInput',
        ];

        foreach ($errorFields as $field) {
            if (Arr::exists($fieldToElementMap, $field)) {
                return $fieldToElementMap[$field];
            }

            if (Str::startsWith($field, 'patients.')) {
                $parts = Str::of($field)->explode('.');

                if (collect($parts)->count() >= 3) {
                    $patientIndex = $parts[1];
                    $patientField = $parts[2];

                    $patientFieldMap = [
                        'last_name' => "patientLastNameInput-{$patientIndex}",
                        'first_name' => "patientFirstNameInput-{$patientIndex}",
                        'sex' => "patientSexDropdownBtn-{$patientIndex}",
                        'age' => "patientAgeInput-{$patientIndex}",
                    ];

                    if (Arr::exists($patientFieldMap, $patientField)) {
                        return $patientFieldMap[$patientField];
                    }
                }
            }
        }

        return null;
    }

    public function render()
    {
        $occupations = Occupation::all();

        return view('livewire.client.client-registration', ['occupations' => $occupations]);
    }
}
