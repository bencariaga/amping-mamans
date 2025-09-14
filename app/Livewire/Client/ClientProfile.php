<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Authentication\Occupation;
use App\Models\User\Member;
use App\Models\User\Client;
use App\Models\User\Applicant;
use App\Models\User\Contact;
use App\Models\User\Patient;

class ClientProfile extends Component
{
    public string $applicantId;
    public $applicant;
    public $occupations;

    public string $first_name = '';
    public string $middle_name = '';
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
    public string $street = '';
    public ?string $occupation_id = null;
    public string $custom_occupation = '';
    public string $job_status = '';
    public ?float $monthly_income = null;
    public string $house_occup_status = '';
    public string $lot_occup_status = '';
    public string $phic_affiliation = '';
    public ?string $phic_category = null;
    public string $representing_patient = '';
    public ?int $patient_count = null;
    public array $patients = [];
    public string $deleteConfirmationText = '';

    protected $rules = [
        'first_name' => 'required|string|max:20',
        'middle_name' => 'nullable|string|max:20',
        'last_name' => 'required|string|max:20',
        'suffix' => 'nullable|string|in:N / A,Jr.,Sr.,II,III,IV,V',
        'birth_date' => 'required|date',
        'sex' => 'required|string|in:Male,Female',
        'civil_status' => 'required|string|in:Single,Married,Widowed,Separated',
        'phone_number' => 'required|string|max:15',
        'barangay' => 'required|string|in:Apopong,Baluan,Batomelong,Buayan,Bula,Calumpang,City Heights,Conel,Dadiangas East,Dadiangas North,Dadiangas South,Dadiangas West,Fatima,Katangawan,Labangal,Lagao,Ligaya,Mabuhay,Olympog,San Isidro,San Jose,Siguel,Sinawal,Tambler,Tinagacan,Upper Labay,Other',
        'street' => 'required|string|max:50',
        'occupation_id' => 'nullable|string|exists:occupations,occupation_id',
        'custom_occupation' => 'nullable|string|max:30',
        'job_status' => 'required|string|in:Permanent,Contractual,Casual',
        'monthly_income' => ['nullable', 'numeric', 'min:0'],
        'house_occup_status' => 'required|string|in:Owner,Renter,House Sharer',
        'lot_occup_status' => 'required|string|in:Owner,Renter,Lot Sharer,Informal Settler',
        'phic_affiliation' => 'required|string|in:Affiliated,Unaffiliated',
        'phic_category' => 'nullable|string|in:Self-Employed,Sponsored,Employed',
        'representing_patient' => 'required|string|in:Self,Other Individual/s,Self and Other Individual/s',
        'patient_count' => 'nullable|integer|min:1|max:3',
        'patients.*.first_name' => 'required_if:patient_count,>0|nullable|string|max:20',
        'patients.*.middle_name' => 'nullable|string|max:20',
        'patients.*.last_name' => 'required_if:patient_count,>0|nullable|string|max:20',
        'patients.*.suffix' => 'nullable|string|in:N / A,Jr.,Sr.,II,III,IV,V',
    ];

    public function mount($applicantId)
    {
        $this->applicant = Applicant::with(['client.member', 'client.contacts', 'client.occupation.data', 'patients.member'])->findOrFail($applicantId);

        $this->fill([
            'first_name' => $this->applicant->client->member->first_name,
            'middle_name' => $this->applicant->client->member->middle_name ?? '',
            'last_name' => $this->applicant->client->member->last_name,
            'suffix' => $this->applicant->client->member->suffix ?? '',
            'birth_date' => $this->applicant->client->birthdate ? Carbon::parse($this->applicant->client->birthdate)->format('Y-m-d') : '',
            'sex' => $this->applicant->client->sex,
            'civil_status' => $this->applicant->client->civil_status,
            'phone_number' => optional($this->applicant->client->contacts->firstWhere('contact_type', 'Application'))->phone_number,
            'province' => $this->applicant->province,
            'city' => $this->applicant->city,
            'municipality' => $this->applicant->municipality,
            'barangay' => $this->applicant->barangay,
            'street' => $this->applicant->street,
            'occupation_id' => $this->applicant->client->occupation_id ?? null,
            'job_status' => $this->applicant->job_status ?? '',
            'monthly_income' => $this->applicant->client->monthly_income !== null ? (float) $this->applicant->client->monthly_income : null,
            'house_occup_status' => $this->applicant->house_occup_status,
            'lot_occup_status' => $this->applicant->lot_occup_status,
            'phic_affiliation' => $this->applicant->phic_affiliation,
            'phic_category' => $this->applicant->phic_category,
            'representing_patient' => $this->applicant->representing_patient,
        ]);

        $this->occupations = Occupation::all();

        if ($this->applicant->client->occupation && !$this->occupations->contains('occupation_id', $this->applicant->client->occupation_id)) {
            $this->custom_occupation = $this->applicant->client->occupation->occupation;
            $this->occupation_id = null;
        } else {
            $this->custom_occupation = '';
        }

        $this->setInitialPatientData();
    }

    private function setInitialPatientData()
    {
        $this->patients = [];
        $existingPatients = $this->applicant->patients;
        $applicantIsPatient = $existingPatients->contains('member_id', $this->applicant->client->member_id);

        if ($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') {
            if ($applicantIsPatient) {
                $selfPatient = $existingPatients->firstWhere('member_id', $this->applicant->client->member_id);

                $this->patients[0] = [
                    'patient_id' => $selfPatient->patient_id,
                    'member_id' => $selfPatient->member_id,
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'suffix' => $this->suffix,
                    'is_applicant' => true,
                ];
            } else {
                $this->patients[0] = [
                    'patient_id' => null,
                    'member_id' => null,
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'suffix' => $this->suffix,
                    'is_applicant' => true,
                ];
            }

            if ($this->representing_patient === 'Self and Other Individual/s') {
                $otherPatients = $existingPatients->filter(fn($p) => $p->member_id !== $this->applicant->client->member_id)->values();

                foreach ($otherPatients as $patient) {
                    if (collect($this->patients)->count() < 3) {
                        $this->patients[] = [
                            'patient_id' => $patient->patient_id,
                            'member_id' => $patient->member->member_id,
                            'first_name' => $patient->member->first_name,
                            'middle_name' => $patient->member->middle_name,
                            'last_name' => $patient->member->last_name,
                            'suffix' => $patient->member->suffix,
                            'is_applicant' => false,
                        ];
                    }
                }
            }

            $this->patient_count = collect($this->patients)->count();
        } elseif ($this->representing_patient === 'Other Individual/s') {
            $allOtherPatients = $existingPatients->values();

            foreach ($allOtherPatients as $patient) {
                if (collect($this->patients)->count() < 3) {
                    $this->patients[] = [
                        'patient_id' => $patient->patient_id,
                        'member_id' => $patient->member->member_id,
                        'first_name' => $patient->member->first_name,
                        'middle_name' => $patient->member->middle_name,
                        'last_name' => $patient->member->last_name,
                        'suffix' => $patient->member->suffix,
                        'is_applicant' => false,
                    ];
                }
            }
            $this->patient_count = collect($this->patients)->count();
        } else {
            $this->patient_count = null;
        }

        if ($this->patient_count !== null) {
            $currentPatientCount = collect($this->patients)->count();

            for ($i = $currentPatientCount; $i < $this->patient_count; $i++) {
                $this->patients[] = [
                    'patient_id' => null,
                    'member_id' => null,
                    'first_name' => '',
                    'middle_name' => '',
                    'last_name' => '',
                    'suffix' => '',
                    'is_applicant' => false,
                ];
            }
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

    public function updated($propertyName)
    {
        if ($propertyName === 'monthly_income' && $this->monthly_income === '') {
            $this->monthly_income = null;
        }

        $this->validateOnly($propertyName);

        if (Str::startsWith($propertyName, 'first_name') || Str::startsWith($propertyName, 'middle_name') || Str::startsWith($propertyName, 'last_name') || $propertyName === 'suffix') {
            if (($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') && isset($this->patients[0])) {
                $this->copyApplicantToPatient(0);
            }
        }

        if ($propertyName === 'custom_occupation') {
            if ($this->custom_occupation) {
                $this->occupation_id = null;
            }
        } elseif ($propertyName === 'occupation_id') {
            if ($this->occupation_id) {
                $this->custom_occupation = '';
            }
        }

        $this->dispatch('update-ui-elements');
    }

    public function setSuffix($value)
    {
        $this->suffix = $value === '' ? null : $value;

        if (($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') && isset($this->patients[0])) {
            $this->copyApplicantToPatient(0);
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

    public function setJobStatus($value)
    {
        $this->job_status = $value;
    }

    public function setOccupation(?string $value)
    {
        $this->occupation_id = $value;

        if ($value) {
            $this->custom_occupation = '';
        }
    }

    public function setPhicAffiliation($value)
    {
        $this->phic_affiliation = $value;

        if ($this->phic_affiliation === 'Unaffiliated') {
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
        $this->patients = [];

        if ($this->representing_patient === 'Self') {
            $this->patient_count = 1;
            $this->copyApplicantToPatient(0);
        } elseif ($this->representing_patient === 'Self and Other Individual/s') {
            $this->patient_count = 1;
            $this->copyApplicantToPatient(0);
        } elseif ($this->representing_patient === 'Other Individual/s') {
            $this->patient_count = 1;
            $this->patients[] = ['first_name' => '', 'middle_name' => '', 'last_name' => '', 'suffix' => '', 'patient_id' => null, 'member_id' => null, 'is_applicant' => false];
        } else {
            $this->patient_count = null;
        }
    }

    public function setPatientCount($value)
    {
        if ($value === '' || $value === null) {
            $this->patient_count = null;
            $this->patients = [];
            return;
        }

        $newCount = (int) $value;
        $currentCount = collect($this->patients)->count();

        if ($newCount > $currentCount) {
            for ($i = $currentCount; $i < $newCount; $i++) {
                $this->patients[] = ['first_name' => '', 'middle_name' => '', 'last_name' => '', 'suffix' => '', 'patient_id' => null, 'member_id' => null, 'is_applicant' => false];
            }
        } elseif ($newCount < $currentCount) {
            $this->patients = collect($this->patients)->slice(0, $newCount)->all();
        }

        $this->patient_count = $newCount;

        if (($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') && isset($this->patients[0])) {
            $this->copyApplicantToPatient(0);
        }
    }

    public function setPatientSuffix($index, $value)
    {
        if (isset($this->patients[$index])) {
            $this->patients[$index]['suffix'] = $value;
        }
    }

    public function copyApplicantToPatient($index)
    {
        if (!isset($this->patients[$index])) {
            $this->patients[$index] = [];
        }

        $this->patients[$index]['first_name'] = $this->first_name;
        $this->patients[$index]['middle_name'] = $this->middle_name;
        $this->patients[$index]['last_name'] = $this->last_name;
        $this->patients[$index]['suffix'] = $this->suffix ?: null;
        $this->patients[$index]['is_applicant'] = true;
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function () {
            $this->applicant->client->member->update([
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'last_name' => $this->last_name,
                'suffix' => $this->suffix ?: null,
                'full_name' => Str::of("{$this->first_name} {$this->middle_name} {$this->last_name} {$this->suffix}")->trim(),
            ]);

            $this->applicant->client->update([
                'birthdate' => $this->birth_date,
                'sex' => $this->sex,
                'civil_status' => $this->civil_status,
                'occupation_id' => $this->occupation_id,
                'monthly_income' => $this->monthly_income,
            ]);

            if ($this->custom_occupation && !$this->occupation_id) {
                $occupation = Occupation::firstOrCreate(
                    ['occupation' => Str::upper($this->custom_occupation)],
                    ['data_id' => $this->generateNextId('DATA', 'data', 'data_id')]
                );

                $this->applicant->client->update(['occupation_id' => $occupation->occupation_id]);
            } elseif (!$this->custom_occupation && $this->occupation_id) {
                $this->applicant->client->update(['occupation_id' => $this->occupation_id]);
            }

            $contact = $this->applicant->client->contacts->firstWhere('contact_type', 'Application');

            if ($contact) {
                $contact->update(['phone_number' => $this->phone_number]);
            } else {
                Contact::create([
                    'contact_id' => $this->generateNextId('CONTACT', 'contacts', 'contact_id'),
                    'client_id' => $this->applicant->client->client_id,
                    'contact_type' => 'Application',
                    'phone_number' => $this->phone_number,
                ]);
            }

            $this->applicant->update([
                'province' => $this->province,
                'city' => $this->city,
                'municipality' => $this->municipality,
                'barangay' => $this->barangay,
                'street' => $this->street,
                'job_status' => $this->job_status,
                'representing_patient' => $this->representing_patient,
                'house_occup_status' => $this->house_occup_status,
                'lot_occup_status' => $this->lot_occup_status,
                'phic_affiliation' => $this->phic_affiliation,
                'phic_category' => $this->phic_affiliation === 'Affiliated' ? $this->phic_category : null,
            ]);

            $existingPatientMemberIds = $this->applicant->patients->pluck('member_id');
            $currentPatientMemberIds = collect();

            foreach ($this->patients as $patientData) {
                if (($this->representing_patient === 'Self' || $this->representing_patient === 'Self and Other Individual/s') && ($patientData['is_applicant'] ?? false)) {
                    Patient::firstOrCreate(
                        ['applicant_id' => $this->applicant->applicant_id, 'member_id' => $this->applicant->client->member_id],
                        ['patient_id' => $this->generateNextId('PATIENT', 'patients', 'patient_id')]
                    );

                    $currentPatientMemberIds->push($this->applicant->client->member_id);
                } else {
                    if (!empty($patientData['first_name']) && !empty($patientData['last_name'])) {
                        $pMember = null;

                        if (!empty($patientData['member_id'])) {
                            $pMember = Member::find($patientData['member_id']);
                        }

                        $suffixForMember = Str::of($patientData['suffix'])->trim()->isNotEmpty() ? $patientData['suffix'] : null;

                        if ($pMember) {
                            $pMember->update([
                                'first_name' => $patientData['first_name'],
                                'middle_name' => $patientData['middle_name'],
                                'last_name' => $patientData['last_name'],
                                'suffix' => $suffixForMember,
                                'full_name' => Str::of("{$patientData['first_name']} {$patientData['middle_name']} {$patientData['last_name']} {$patientData['suffix']}")->trim(),
                            ]);
                        } else {
                            $pMemberId = $this->generateNextId('MEMBER', 'members', 'member_id');
                            $pMember = Member::create([
                                'member_id' => $pMemberId,
                                'account_id' => $this->applicant->client->member->account_id,
                                'member_type' => 'Patient',
                                'first_name' => $patientData['first_name'],
                                'middle_name' => $patientData['middle_name'],
                                'last_name' => $patientData['last_name'],
                                'suffix' => $suffixForMember,
                                'full_name' => Str::of("{$patientData['first_name']} {$patientData['middle_name']} {$patientData['last_name']} {$patientData['suffix']}")->trim(),
                            ]);
                        }

                        $currentPatientMemberIds->push($pMember->member_id);

                        Patient::firstOrCreate(
                            ['applicant_id' => $this->applicant->applicant_id, 'member_id' => $pMember->member_id],
                            ['patient_id' => $this->generateNextId('PATIENT', 'patients', 'patient_id')]
                        );
                    }
                }
            }

            $membersToDelete = $existingPatientMemberIds->diff($currentPatientMemberIds);

            foreach ($membersToDelete as $memberIdToDelete) {
                if ($memberIdToDelete !== $this->applicant->client->member->member_id) {
                    $clientToDelete = Client::where('member_id', $memberIdToDelete)->first();

                    if ($clientToDelete) {
                        $clientToDelete->delete();
                    }

                    $patientToDelete = Patient::where('applicant_id', $this->applicant->applicant_id)->where('member_id', $memberIdToDelete)->first();

                    if ($patientToDelete) {
                        $patientToDelete->delete();
                    }

                    $memberToDeleteRecord = Member::find($memberIdToDelete);

                    if ($memberToDeleteRecord) {
                        $memberToDeleteRecord->delete();
                    }
                }
            }
        });

        session()->flash('success', 'Applicant profile has been updated successfully.');
        $this->redirect(route('profiles.applicants.show', ['applicant' => $this->applicantId]));
    }

    public function destroy()
    {
        $fullName = Str::of("{$this->first_name} {$this->middle_name} {$this->last_name} {$this->suffix}")->trim();

        if ($this->deleteConfirmationText !== $fullName) {
            session()->flash('error', 'Confirmation text does not match.');
            return;
        }

        DB::transaction(function () {
            $applicant = $this->applicant;
            $client = $applicant->client;
            $member = $client->member;

            $contactIds = $client->contacts->pluck('contact_id')->toArray();

            if (!empty($contactIds)) {
                DB::table('messages')->whereIn('contact_id', $contactIds)->delete();
            }

            DB::table('contacts')->where('client_id', $client->client_id)->delete();

            $applicationIds = DB::table('applications')->where('applicant_id', $applicant->applicant_id)->pluck('application_id')->toArray();

            if (!empty($applicationIds)) {
                DB::table('guarantee_letters')->whereIn('application_id', $applicationIds)->delete();
                DB::table('applications')->whereIn('application_id', $applicationIds)->delete();
            }

            $patientMemberIds = DB::table('patients')->where('applicant_id', $applicant->applicant_id)->pluck('member_id')->toArray();

            DB::table('patients')->where('applicant_id', $applicant->applicant_id)->delete();

            foreach ($patientMemberIds as $pmid) {
                DB::table('clients')->where('member_id', $pmid)->delete();
                DB::table('members')->where('member_id', $pmid)->delete();
            }

            DB::table('files')->where('member_id', $member->member_id)->delete();

            DB::table('applicants')->where('applicant_id', $applicant->applicant_id)->delete();

            DB::table('clients')->where('client_id', $client->client_id)->delete();

            DB::table('members')->where('member_id', $member->member_id)->delete();
        });

        DB::commit();
        session()->flash('success', 'Applicant and associated data deleted successfully.');
        return redirect()->route('profiles.applicants.list');
    }

    public function submitForm()
    {
        $this->update();
    }

    public function render()
    {
        return view('livewire.client.client-profile');
    }
}
