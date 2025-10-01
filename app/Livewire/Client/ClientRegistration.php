<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Authentication\Account;
use App\Models\Authentication\Occupation;
use App\Models\Storage\Data;
use App\Models\User\Member;
use App\Models\User\Client;
use App\Models\User\Applicant;
use App\Models\User\Contact;
use App\Models\User\Patient;

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
    public string $occupation_status = '';
    public $monthly_income = 0;
    public string $house_occup_status = '';
    public string $lot_occup_status = '';
    public string $phic_affiliation = '';
    public ?string $phic_category = null;
    public string $representing_patient = '';
    public array $patients = [];

    protected array $rules = [
        'first_name' => 'required|string|max:20',
        'middle_name' => 'nullable|string|max:20',
        'last_name' => 'required|string|max:20',
        'suffix' => 'nullable|string|in:Sr.,Jr.,II,III,IV,V',
        'birth_date' => 'required|date',
        'sex' => 'required|string|in:Male,Female',
        'civil_status' => 'required|string|in:Single,Married,Widowed,Separated',
        'phone_number' => 'required|string|max:15|unique:contacts,phone_number',
        'barangay' => 'required|string',
        'occupation_id' => 'required|string|exists:occupations,occupation_id',
        'custom_occupation' => 'nullable|string|max:30',
        'job_status' => 'required|string|in:Permanent,Contractual,Casual',
        'occupation_status' => 'nullable|string|in:Employed,Self-Employed,Job Order,Private Individual',
        'monthly_income' => 'required|integer|min:0|max:9999999',
        'house_occup_status' => 'required|string|in:Owner,Renter,House Sharer',
        'lot_occup_status' => 'required|string|in:Owner,Renter,Lot Sharer,Informal Settler',
        'phic_affiliation' => 'required|string|in:Affiliated,Unaffiliated',
        'phic_category' => 'nullable|string|in:Self-Employed,Sponsored,Employed',
        'representing_patient' => 'required|string|in:Self,Other Individual',
        'patients.*.first_name' => 'required|string|max:20',
        'patients.*.middle_name' => 'nullable|string|max:20',
        'patients.*.last_name' => 'required|string|max:20',
        'patients.*.suffix' => 'nullable|string|max:5',
    ];

    public $step = 1;

    public function nextStep()
    {
        $this->validateStep($this->step);
        $this->step++;
    }

    public function prevStep()
    {
        $this->step--;
    }

    public function validateStep($step)
    {
        // Add validation rules per step
        if ($step === 1) {
            $this->validate([
                'first_name' => 'required|string|max:20',
                'middle_name' => 'nullable|string|max:20',
                'last_name' => 'required|string|max:20',
                'suffix' => 'nullable|string|in:Sr.,Jr.,II,III,IV,V'
            ]);
        } elseif ($step === 2) {
            $this->validate([
                'sex' => 'required|string',
                'birth_date' => 'required|date',
                'phone_number' => 'required|string|max:15|unique:contacts,phone_number',
                'civil_status' => 'required|string|in:Single,Married,Widowed,Separated',
            ]);
        } elseif ($step === 3) {
            $this->validate([
                'house_occup_status' => 'required|string|in:Owner,Renter,House Sharer',
                'lot_occup_status' => 'required|string|in:Owner,Renter,Lot'
            ]);
        } elseif ($step === 4) {
            $this->validate([
                'job_status' => 'required|string|in:Permanent,Contractual,Casual',
                'occupation_id' => 'required|string|exists:occupations,occupation_id',
                'custom_occupation' => 'nullable|string|max:30',
                'monthly_income' => 'required|integer|min:0|max:9999999',
            ]);
        } elseif ($step === 5) {
            $this->validate([
                'representing_patient' => 'required|string|in:Self,Other Individual',
                'phic_affiliation' => 'required|string|in:Affiliated,Unaffiliated',
                'phic_category' => 'nullable|string|in:Self-Employed,Sponsored,Employed',
                'patients.*.first_name' => 'required|string|max:20',
                'patients.*.middle_name' => 'nullable|string|max:20',
                'patients.*.last_name' => 'required|string|max:20',
                'patients.*.suffix' => 'nullable|string|max:5',
            ]);
        }       
    }

    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $last = $max ? (int) Str::afterLast($max, '-') : 0;
        return $base . '-' . Str::padLeft($last + 1, 9, '0');
    }

    public function mount()
    {
        $this->job_status = '';
        $this->monthly_income = 0;

        $this->patients = [
            1 => ['first_name' => '', 'middle_name' => '', 'last_name' => '', 'suffix' => '']
        ];
    }

    public function updated($propertyName)
    {
        if (Str::startsWith($propertyName, 'patients.')) {
            $this->validateOnly($propertyName);
        } else {
            $this->validateOnly($propertyName);
        }
    }

    public function updatedPhicAffiliation()
    {
        if ($this->phic_affiliation !== 'Affiliated') {
            $this->phic_category = null;
        }
    }

    public function updatedRepresentingPatient()
    {
        if ($this->representing_patient === 'Self') {
            $this->copyApplicantToPatient(1);
        } else {
            $this->clearPatient(1);
        }
    }

    public function updatedFirstName()
    {
        if ($this->representing_patient === 'Self') {
            $this->copyApplicantToPatient(1);
        }
    }

    public function updatedMiddleName()
    {
        if ($this->representing_patient === 'Self') {
            $this->copyApplicantToPatient(1);
        }
    }

    public function updatedLastName()
    {
        if ($this->representing_patient === 'Self') {
            $this->copyApplicantToPatient(1);
        }
    }

    public function updatedSuffix()
    {
        if ($this->representing_patient === 'Self') {
            $this->copyApplicantToPatient(1);
        }
    }

    public function setSuffix($value)
    {
        $this->suffix = $value;
        if ($this->representing_patient === 'Self') {
            $this->copyApplicantToPatient(1);
        }
    }

    public function setSex($value)
    {
        $this->sex = $value;
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
        $this->custom_occupation = '';
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

    public function setRepresentingPatient($value)
    {
        $this->representing_patient = $value;

        if ($this->representing_patient === 'Self') {
            $this->copyApplicantToPatient(1);
        } else {
            $this->clearPatient(1);
        }
    }

    public function setPatientSuffix($index, $value)
    {
        $this->patients[$index]['suffix'] = $value;
    }

    public function copyApplicantToPatient($index)
    {
        $this->patients[$index] = [
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
        ];
    }

    public function clearPatient($index)
    {
        $this->patients[$index] = [
            'first_name' => '',
            'middle_name' => '',
            'last_name' => '',
            'suffix' => '',
        ];
    }

    private function normalizePhoneNumber(string $value): string
    {
        $raw = Str::of($value)->trim();
        $clean = Str::of($raw)->replaceMatches('/[^0-9+]/', '')->toString();

        if (Str::startsWith($clean, '+')) {
            $clean = Str::substr($clean, 1);
        }

        $clean = Str::of($clean)->replaceMatches('/[^0-9]/', '');

        if (Str::startsWith($clean, '63')) {
            $clean = '0' . Str::substr($clean, 2);
        } elseif (Str::startsWith($clean, '9')) {
            $clean = '0' . $clean;
        } elseif (!Str::startsWith($clean, '0')) {
            $clean = '0' . $clean;
        }

        if (strlen($clean) >= 11) {
            $part1 = Str::substr($clean, 0, 4);
            $part2 = Str::substr($clean, 4, 3);
            $part3 = Str::substr($clean, 7, 4);
            $formatted = $part1;

            if ($part2 !== false && $part2 !== '') {
                $formatted .= '-' . $part2;
            }

            if ($part3 !== false && $part3 !== '') {
                $formatted .= '-' . $part3;
            }

            return $formatted;
        }

        if (strlen($clean) > 4) {
            $part1 = Str::substr($clean, 0, 4);
            $part2 = Str::substr($clean, 4);
            return $part1 . ($part2 ? '-' . $part2 : '');
        }

        return $clean;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $dataId      = $this->generateNextId('DATA', 'data', 'data_id');
            $acctId      = $this->generateNextId('ACCOUNT', 'accounts', 'account_id');
            $memberId    = $this->generateNextId('MEMBER', 'members', 'member_id');
            $clientId    = $this->generateNextId('CLIENT', 'clients', 'client_id');
            $contactId   = $this->generateNextId('CONTACT', 'contacts', 'contact_id');
            $applicantId = $this->generateNextId('APPLICANT', 'applicants', 'applicant_id');

            Data::create([
                'data_id'     => $dataId,
                'data_status' => 'Unarchived',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            Account::create([
                'account_id'     => $acctId,
                'data_id'        => $dataId,
                'account_status' => 'Active',
                'registered_at'  => now(),
            ]);

            $occId = $this->occupation_id;

            if ($this->custom_occupation) {
                $d2 = $this->generateNextId('DATA', 'data', 'data_id');

                Data::create([
                    'data_id'     => $d2,
                    'data_status' => 'Unarchived',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                $occupation = Occupation::create([
                    'occupation_id' => $this->generateNextId('OCCUP', 'occupations', 'occupation_id'),
                    'data_id'       => $d2,
                    'occupation'    => $this->custom_occupation,
                ]);

                $occId = $occupation->occupation_id;
            }

            $member = Member::create([
                'member_id'   => $memberId,
                'account_id'  => $acctId,
                'member_type' => 'Applicant',
                'first_name'  => $this->first_name,
                'middle_name' => $this->middle_name,
                'last_name'   => $this->last_name,
                'suffix'      => $this->suffix === '' ? null : $this->suffix,
                'full_name'   => Str::of("{$this->first_name} {$this->middle_name} {$this->last_name} {$this->suffix}")->trim(),
            ]);

            $client = Client::create([
                'client_id'      => $clientId,
                'member_id'      => $memberId,
                'occupation_id'  => $occId,
                'birthdate'      => $this->birth_date,
                'sex'            => $this->sex,
                'civil_status'   => $this->civil_status,
                'monthly_income' => is_numeric($this->monthly_income) ? (float) $this->monthly_income : 0,
            ]);

            $formattedPhone = $this->normalizePhoneNumber($this->phone_number);

            Contact::create([
                'contact_id'   => $contactId,
                'client_id'    => $clientId,
                'contact_type' => 'Application',
                'phone_number' => $formattedPhone,
            ]);

            $phicCategory = $this->phic_affiliation === 'Affiliated' ? $this->phic_category : null;

            Applicant::create([
                'applicant_id'         => $applicantId,
                'client_id'            => $clientId,
                'province'             => $this->province,
                'city'                 => $this->city,
                'municipality'         => $this->municipality,
                'barangay'             => $this->barangay,
                'subdivision'          => $this->subdivision,
                'purok'                => $this->purok,
                'sitio'                => $this->sitio,
                'street'               => $this->street,
                'phase'                => $this->phase,
                'block_number'         => $this->block_number,
                'house_number'         => $this->house_number,
                'job_status'           => $this->job_status,
                'representing_patient' => $this->representing_patient,
                'house_occup_status'   => $this->house_occup_status,
                'lot_occup_status'     => $this->lot_occup_status,
                'phic_affiliation'     => $this->phic_affiliation,
                'phic_category'        => $phicCategory,
            ]);

            if ($this->representing_patient === 'Self') {
                Patient::create([
                    'patient_id'   => $this->generateNextId('PATIENT', 'patients', 'patient_id'),
                    'applicant_id' => $applicantId,
                    'member_id'    => $memberId,
                ]);
            } else {
                $pMemberId = $this->generateNextId('MEMBER', 'members', 'member_id');

                Member::create([
                    'member_id'   => $pMemberId,
                    'account_id'  => $acctId,
                    'member_type' => 'Patient',
                    'first_name'  => $this->patients[1]['first_name'],
                    'middle_name' => $this->patients[1]['middle_name'],
                    'last_name'   => $this->patients[1]['last_name'],
                    'suffix'      => $this->patients[1]['suffix'] === '' ? null : $this->patients[1]['suffix'],
                    'full_name'   => Str::of("{$this->patients[1]['first_name']} " . ($this->patients[1]['middle_name'] ?? '') . " " . ($this->patients[1]['last_name'] ?? '') . " " . ($this->patients[1]['suffix'] ?? ''))->trim(),
                ]);

                Patient::create([
                    'patient_id'   => $this->generateNextId('PATIENT', 'patients', 'patient_id'),
                    'applicant_id' => $applicantId,
                    'member_id'    => $pMemberId,
                ]);
            }
        });

        session()->flash('success', 'Applicant has been added successfully.');
        $this->reset();
        $this->redirectRoute('profiles.applicants.list');
    }

    public function render()
    {
        $occupations = Occupation::all();
        return view('livewire.client.client-registration', ['occupations' => $occupations]);
    }
}
