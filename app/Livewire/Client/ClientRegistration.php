<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\Attributes\Validate;
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
    #[Validate('required|string|max:20')]
    public string $first_name = '';

    #[Validate('nullable|string|max:20')]
    public string $middle_name = '';

    #[Validate('required|string|max:20')]
    public string $last_name = '';

    #[Validate('nullable|string|in:Jr.,Sr.,II,III,IV,V')]
    public string $suffix = '';

    #[Validate('required|date')]
    public string $birth_date = '';

    #[Validate('required|string|in:Male,Female')]
    public string $sex = '';

    #[Validate('required|string|in:Single,Married,Widowed,Separated')]
    public string $civil_status = '';

    #[Validate('required|string|max:15|unique:contacts,phone_number')]
    public string $phone_number = '';

    public string $province = 'South Cotabato';
    public string $city = 'General Santos';
    public string $municipality = 'N / A';

    #[Validate('required|string|in:Apopong,Baluan,Batomelong,Buayan,Bula,Calumpang,City Heights,Conel,Dadiangas East,Dadiangas North,Dadiangas South,Dadiangas West,Fatima,Katangawan,Labangal,Lagao,Ligaya,Mabuhay,Olympog,San Isidro,San Jose,Siguel,Sinawal,Tambler,Tinagacan,Upper Labay,Other')]
    public string $barangay = '';

    #[Validate('required|string|max:50')]
    public string $street = '';

    #[Validate('nullable|string|exists:occupations,occupation_id')]
    public ?string $occupation_id = null;

    #[Validate('nullable|string|max:30')]
    public string $custom_occupation = '';

    #[Validate('required|string|in:Permanent,Contractual,Casual')]
    public string $job_status = '';

    #[Validate('nullable', 'numeric', 'min:0')]
    public ?float $monthly_income = null;

    #[Validate('required|string|in:Owner,Renter,House Sharer')]
    public string $house_occup_status = '';

    #[Validate('required|string|in:Owner,Renter,Lot Sharer,Informal Settler')]
    public string $lot_occup_status = '';

    #[Validate('required|string|in:Affiliated,Unaffiliated')]
    public string $phic_affiliation = '';

    #[Validate('nullable|string|in:Self-Employed,Sponsored,Employed')]
    public ?string $phic_category = null;

    #[Validate('required|string|in:Self,Other Individual/s,Self and Other Individual/s')]
    public string $representing_patient = '';

    #[Validate('nullable|integer|min:1|max:3')]
    public ?int $patient_count = null;

    public array $patients = [];

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
        $this->monthly_income = null;
        $this->patients = [
            1 => ['first_name' => '', 'middle_name' => '', 'last_name' => '', 'suffix' => ''],
            2 => ['first_name' => '', 'middle_name' => '', 'last_name' => '', 'suffix' => ''],
            3 => ['first_name' => '', 'middle_name' => '', 'last_name' => '', 'suffix' => ''],
        ];
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'monthly_income' && $this->monthly_income === '') {
            $this->monthly_income = null;
        }

        if (Str::startsWith($propertyName, 'patients.')) {
            $this->validateOnly($propertyName, [
                "patients.*.first_name" => "required_if:patient_count,>0|nullable|string|max:20",
                "patients.*.middle_name" => "nullable|string|max:20",
                "patients.*.last_name" => "required_if:patient_count,>0|nullable|string|max:20",
                "patients.*.suffix" => "nullable|string|max:5",
            ]);
        } else {
            $this->validateOnly($propertyName);
        }
    }

    public function updatedPhicAffiliation()
    {
        if ($this->phic_affiliation === 'Unaffiliated') {
            $this->phic_category = null;
        }
    }

    public function updatedRepresentingPatient()
    {
        if ($this->representing_patient === 'Self') {
            $this->patient_count = 1;
            $this->copyApplicantToPatient(1);
        } elseif ($this->representing_patient === 'Self and Other Individual/s') {
            $this->patient_count = 1;
            $this->copyApplicantToPatient(1);
        } elseif ($this->representing_patient === 'Other Individual/s') {
            $this->patient_count = null;
            $this->clearPatient(1);
        } else {
            $this->patient_count = null;
            $this->clearPatient(1);
            $this->clearPatient(2);
            $this->clearPatient(3);
        }
    }

    public function updatedPatientCount($value)
    {
        if ($value === '' || $value === null) {
            $this->patient_count = null;
        } else {
            $this->patient_count = (int) $value;
        }

        for ($i = 1; $i <= 3; $i++) {
            if ($this->patient_count === null || $i > $this->patient_count) {
                $this->clearPatient($i);
            }
        }

        if ($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') {
            $this->copyApplicantToPatient(1);
        }
    }

    public function updatedFirstName()
    {
        if ($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') {
            $this->copyApplicantToPatient(1);
        }
    }

    public function updatedMiddleName()
    {
        if ($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') {
            $this->copyApplicantToPatient(1);
        }
    }

    public function updatedLastName()
    {
        if ($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') {
            $this->copyApplicantToPatient(1);
        }
    }

    public function updatedSuffix()
    {
        if ($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') {
            $this->copyApplicantToPatient(1);
        }
    }

    public function setSuffix($value)
    {
        $this->suffix = $value;
        if ($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') {
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
    }

    public function setPhicAffiliation($value)
    {
        $this->phic_affiliation = $value;
    }

    public function setPhicCategory($value)
    {
        $this->phic_category = $value;
    }

    public function setRepresentingPatient($value)
    {
        $this->representing_patient = $value;

        if ($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') {
            $this->setPatientCount(1);
            $this->copyApplicantToPatient(1);
        } elseif ($this->representing_patient === 'Other Individual/s') {
            $this->setPatientCount(1);
            $this->clearPatient(1);
            $this->clearPatient(2);
            $this->clearPatient(3);
        } else {
            $this->setPatientCount('');
            $this->clearPatient(1);
            $this->clearPatient(2);
            $this->clearPatient(3);
        }
    }

    public function setPatientCount($value)
    {
        if ($value === '' || $value === null) {
            $this->patient_count = null;
            return;
        }

        $this->patient_count = (int) $value;
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
                'monthly_income' => $this->monthly_income,
            ]);

            Contact::create([
                'contact_id'   => $contactId,
                'client_id'    => $clientId,
                'contact_type' => 'Application',
                'phone_number' => $this->phone_number,
            ]);

            $phicCategory = $this->phic_affiliation === 'Affiliated' ? $this->phic_category : null;

            Applicant::create([
                'applicant_id'         => $applicantId,
                'client_id'            => $clientId,
                'province'             => $this->province,
                'city'                 => $this->city,
                'municipality'         => $this->municipality,
                'barangay'             => $this->barangay,
                'street'               => $this->street,
                'job_status'           => $this->job_status,
                'representing_patient' => $this->representing_patient,
                'house_occup_status'   => $this->house_occup_status,
                'lot_occup_status'     => $this->lot_occup_status,
                'phic_affiliation'     => $this->phic_affiliation,
                'phic_category'        => $phicCategory,
            ]);

            if ($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') {
                Patient::create([
                    'patient_id'   => $this->generateNextId('PATIENT', 'patients', 'patient_id'),
                    'applicant_id' => $applicantId,
                    'member_id'    => $memberId,
                ]);
            }

            if ($this->representing_patient === 'Other Individual/s' || ($this->representing_patient === 'Self and Other Individual/s' && $this->patient_count > 1)) {
                $start = ($this->representing_patient === 'Self and Other Individual/s') ? 2 : 1;

                for ($i = $start; $i <= $this->patient_count; $i++) {
                    $pMemberId = $this->generateNextId('MEMBER', 'members', 'member_id');

                    Member::create([
                        'member_id'   => $pMemberId,
                        'account_id'  => $acctId,
                        'member_type' => 'Patient',
                        'first_name'  => $this->patients[$i]['first_name'],
                        'middle_name' => $this->patients[$i]['middle_name'],
                        'last_name'   => $this->patients[$i]['last_name'],
                        'suffix'      => $this->patients[$i]['suffix'] === '' ? null : $this->patients[$i]['suffix'],
                        'full_name'   => Str::of("{$this->patients[$i]['first_name']} " . ($this->patients[$i]['middle_name'] ?? '') . " " . ($this->patients[$i]['last_name'] ?? '') . " " . ($this->patients[$i]['suffix'] ?? ''))->trim(),
                    ]);

                    Patient::create([
                        'patient_id'   => $this->generateNextId('PATIENT', 'patients', 'patient_id'),
                        'applicant_id' => $applicantId,
                        'member_id'    => $pMemberId,
                    ]);
                }
            }
        });

        DB::commit();
        session()->flash('success', 'Applicant has been added successfully.');
        $this->reset();
        return redirect()->route('profiles.applicants.list');
    }

    public function submitForm()
    {
        $this->save();
    }

    public function render()
    {
        $occupations = Occupation::all();
        return view('livewire.client.client-registration', ['occupations' => $occupations]);
    }
}
