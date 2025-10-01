<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Authentication\Account;
use App\Models\Authentication\Occupation;
use App\Models\Storage\Data;
use App\Models\User\Member;
use App\Models\User\Client;
use App\Models\User\Applicant;
use App\Models\User\Contact;
use App\Models\User\Patient;

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
    public string $occupation_status = '';
    public $monthly_income = 0;
    public string $house_occup_status = '';
    public string $lot_occup_status = '';
    public string $phic_affiliation = '';
    public ?string $phic_category = null;
    public string $representing_patient = '';
    public array $patients = [];
    public ?string $client_id = null;
    public ?string $member_id = null;
    public ?string $account_id = null;
    public ?string $contact_id = null;

    protected function rules()
    {
        return [
            'first_name' => 'required|string|max:20',
            'middle_name' => 'nullable|string|max:20',
            'last_name' => 'required|string|max:20',
            'suffix' => 'nullable|string|in:Sr.,Jr.,II,III,IV,V',
            'birth_date' => 'required|date',
            'sex' => 'required|string|in:Male,Female',
            'civil_status' => 'required|string|in:Single,Married,Widowed,Separated',
            'phone_number' => ['required', 'string', 'max:15', Rule::unique('contacts', 'phone_number')->ignore($this->contact_id, 'contact_id')],
            'barangay' => 'nullable|string',
            'occupation_id' => 'nullable|string|exists:occupations,occupation_id',
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
    }

    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $last = $max ? (int) Str::afterLast($max, '-') : 0;
        $next = $last + 1;
        $padded = Str::padLeft($next, 9, '0');
        return "{$base}-{$padded}";
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
                $this->monthly_income = isset($client->monthly_income) ? (int)$client->monthly_income : 0;
                $this->occupation_id = $client->occupation_id;
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
            $this->representing_patient = $applicant->representing_patient ?? $this->representing_patient;
            $this->house_occup_status = $applicant->house_occup_status ?? $this->house_occup_status;
            $this->lot_occup_status = $applicant->lot_occup_status ?? $this->lot_occup_status;
            $this->phic_affiliation = $applicant->phic_affiliation ?? $this->phic_affiliation;
            $this->phic_category = $applicant->phic_category ?? $this->phic_category;
        }

        $patients = Patient::where('applicant_id', $this->applicantId)->get();

        if ($this->representing_patient === 'Self') {
            $this->patients = [
                1 => ['first_name' => $this->first_name, 'middle_name' => $this->middle_name, 'last_name' => $this->last_name, 'suffix' => $this->suffix]
            ];
        } elseif ($patients->count() > 0) {
            $p = $patients->first();
            $pm = Member::where('member_id', $p->member_id)->first();

            $this->patients = [
                1 => [
                    'first_name' => $pm->first_name ?? '',
                    'middle_name' => $pm->middle_name ?? '',
                    'last_name' => $pm->last_name ?? '',
                    'suffix' => $pm->suffix ?? ''
                ]
            ];
        } else {
            $this->patients = [1 => ['first_name' => '', 'middle_name' => '', 'last_name' => '', 'suffix' => '']];
        }

        $this->dispatch('update-ui-elements');
    }

    public function updatedMonthlyIncome($value)
    {
        $clean = Str::of($value)->replaceMatches('/\\D/', '')->toString();

        if ($clean === '') {
            $this->monthly_income = 0;
            return;
        }

        $clean = Str::ltrim($clean, '0');

        if ($clean === '') {
            $clean = '0';
        }
        $this->monthly_income = $clean;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules());
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

        $clean = Str::of($clean)->replaceMatches('/[^0-9]/', '')->toString();

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

    public function update()
    {
        $this->validate($this->rules());

        $this->monthly_income = Str::of($this->monthly_income)->replaceMatches('/\\D/', '')->toString();

        if ($this->monthly_income === '') {
            $this->monthly_income = 0;
        }

        DB::transaction(function () {
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

            if ($this->member_id) {
                Member::where('member_id', $this->member_id)->update([
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'suffix' => $this->suffix === '' ? null : $this->suffix,
                    'full_name' => Str::of("{$this->first_name} {$this->middle_name} {$this->last_name} {$this->suffix}")->trim(),
                ]);
            }

            if ($this->client_id) {
                $client = Client::where('client_id', $this->client_id)->first();

                if ($client) {
                    $client->occupation_id = $occId;
                    $client->birthdate = $this->birth_date;
                    $client->sex = $this->sex;
                    $client->civil_status = $this->civil_status;
                    $client->monthly_income = is_numeric($this->monthly_income) ? (int) $this->monthly_income : 0;
                    $client->save();
                }
            }

            if ($this->contact_id) {
                Contact::where('contact_id', $this->contact_id)->update([
                    'phone_number' => $this->normalizePhoneNumber($this->phone_number),
                ]);
            }

            Applicant::where('applicant_id', $this->applicantId)->update([
                'province' => $this->province,
                'city' => $this->city,
                'municipality' => $this->municipality,
                'barangay' => $this->barangay,
                'subdivision' => $this->subdivision,
                'purok' => $this->purok,
                'sitio' => $this->sitio,
                'street' => $this->street,
                'phase' => $this->phase,
                'block_number' => $this->block_number,
                'house_number' => $this->house_number,
                'job_status' => $this->job_status,
                'representing_patient' => $this->representing_patient,
                'house_occup_status' => $this->house_occup_status,
                'lot_occup_status' => $this->lot_occup_status,
                'phic_affiliation' => $this->phic_affiliation,
                'phic_category' => $this->phic_affiliation === 'Affiliated' ? $this->phic_category : null,
            ]);

            if ($this->representing_patient === 'Self') {
                $existing = Patient::where('applicant_id', $this->applicantId)->first();

                if (!$existing && $this->member_id) {
                    Patient::create([
                        'patient_id' => $this->generateNextId('PATIENT', 'patients', 'patient_id'),
                        'applicant_id' => $this->applicantId,
                        'member_id' => $this->member_id,
                    ]);
                }
            } else {
                $existing = Patient::where('applicant_id', $this->applicantId)->first();

                if ($existing) {
                    $pMember = Member::where('member_id', $existing->member_id)->first();

                    if ($pMember) {
                        $pMember->update([
                            'first_name' => $this->patients[1]['first_name'],
                            'middle_name' => $this->patients[1]['middle_name'],
                            'last_name' => $this->patients[1]['last_name'],
                            'suffix' => $this->patients[1]['suffix'] === '' ? null : $this->patients[1]['suffix'],
                            'full_name' => Str::of("{$this->patients[1]['first_name']} " . ($this->patients[1]['middle_name'] ?? '') . " " . ($this->patients[1]['last_name'] ?? '') . " " . ($this->patients[1]['suffix'] ?? ''))->trim(),
                        ]);
                    }
                } else {
                    $pMemberId = $this->generateNextId('MEMBER', 'members', 'member_id');

                    Member::create([
                        'member_id' => $pMemberId,
                        'account_id' => $this->account_id,
                        'member_type' => 'Patient',
                        'first_name' => $this->patients[1]['first_name'],
                        'middle_name' => $this->patients[1]['middle_name'],
                        'last_name' => $this->patients[1]['last_name'],
                        'suffix' => $this->patients[1]['suffix'] === '' ? null : $this->patients[1]['suffix'],
                        'full_name' => Str::of("{$this->patients[1]['first_name']} " . ($this->patients[1]['middle_name'] ?? '') . " " . ($this->patients[1]['last_name'] ?? '') . " " . ($this->patients[1]['suffix'] ?? ''))->trim(),
                    ]);

                    Patient::create([
                        'patient_id' => $this->generateNextId('PATIENT', 'patients', 'patient_id'),
                        'applicant_id' => $this->applicantId,
                        'member_id' => $pMemberId,
                    ]);
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
            Account::where('account_id', $this->account_id)->update([
                'account_status' => 'Deactivated',
                'last_deactivated_at' => now(),
            ]);
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
