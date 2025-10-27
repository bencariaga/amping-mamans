<?php

namespace App\Livewire\Client;

use App\Models\Authentication\Account;
use App\Models\Authentication\Occupation;
use App\Models\User\Client;
use App\Models\User\Household;
use App\Models\User\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class HouseholdProfile extends Component
{
    public $household;

    public $household_name;

    public $members = [];

    public $occupations = [];

    protected $listeners = ['clientSelected' => 'handleClientSelection'];

    protected function rules(): array
    {
        return [
            'household_name' => ['required', 'string', 'max:20'],
            'members.*.last_name' => ['required', 'string', 'max:255'],
            'members.*.first_name' => ['required', 'string', 'max:255'],
            'members.*.middle_name' => ['nullable', 'string', 'max:255'],
            'members.*.suffix' => ['nullable', 'string', Rule::in(['Sr.', 'Jr.', 'II', 'III', 'IV', 'V'])],
            'members.*.birthdate' => ['nullable', 'date'],
            'members.*.civil_status' => ['nullable', 'string'],
            'members.*.occupation' => ['nullable', 'string'],
            'members.*.monthly_income' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'members.*.education' => ['nullable', 'string'],
            'members.*.relation_to_head' => ['required', 'string'],
            'members.*.client_id' => ['nullable', 'string', 'max:22'],
            'members.*.is_client' => ['boolean'],
            'members.*.client_type' => ['required', 'string'],
        ];
    }

    public function mount($household = null, $clients = [], $occupations = [])
    {
        $this->household = $household;
        $this->household_name = $household->household_name ?? '';
        $this->occupations = $occupations;

        if (! empty($clients) && $clients[0] !== null) {
            foreach ($clients as $client) {
                $clientType = $client['client_type'] ?? 'HOUSEHOLD_MEMBER';
                $birthdateValue = $client['birthdate'] ?? '';

                if ($clientType === 'PATIENT' && $birthdateValue === null) {
                    $birthdateValue = '';
                }

                $memberData = [
                    'client_id' => $client['client_id'] ?? '',
                    'is_client' => true,
                    'client_type' => $clientType,
                    'read_only_fields' => $this->getReadOnlyFields($clientType),
                    'last_name' => $client['member']['last_name'] ?? '',
                    'first_name' => $client['member']['first_name'] ?? '',
                    'middle_name' => $client['member']['middle_name'] ?? '',
                    'suffix' => $client['member']['suffix'] ?? '',
                    'birthdate' => $birthdateValue,
                    'age' => $client['age'] ?? '',
                    'civil_status' => $client['civil_status'] ?? '',
                    'occupation' => $client['occupation'] ?? '',
                    'monthly_income' => $client['monthly_income'] ?? 0,
                    'education' => $client['education'] ?? '',
                    'relation_to_head' => $client['relation_to_head'] ?? '',
                ];

                $this->members[] = $memberData;
            }
        }

        if (empty($this->members) || $clients[0] === null) {
            $this->addMember();
        }
    }

    private function getReadOnlyFields(string $clientType): array
    {
        if ($clientType === 'APPLICANT') {
            return [
                'last_name',
                'first_name',
                'middle_name',
                'suffix',
                'birthdate',
                'age',
                'civil_status',
                'occupation',
                'monthly_income',
            ];
        } elseif ($clientType === 'PATIENT') {
            return [
                'last_name',
                'first_name',
                'middle_name',
                'suffix',
                'birthdate',
                'age',
            ];
        }

        return [];
    }

    public function addMember()
    {
        $this->members[] = [
            'client_id' => null,
            'is_client' => false,
            'client_type' => 'HOUSEHOLD_MEMBER',
            'read_only_fields' => [],
            'last_name' => '',
            'first_name' => '',
            'middle_name' => '',
            'suffix' => '',
            'birthdate' => '',
            'age' => '',
            'civil_status' => '',
            'occupation' => '',
            'monthly_income' => 0,
            'education' => '',
            'relation_to_head' => '',
        ];
    }

    public function removeMember($index)
    {
        unset($this->members[$index]);
        $this->members = collect($this->members)->values()->all();

        if (empty($this->members)) {
            $this->addMember();
        }
    }

    public function handleClientSelection(array $data)
    {
        $index = (int) $data['index'];
        $clientData = $data['clientData'];
        $clientType = $clientData['client_type'];

        $this->members[$index]['client_id'] = $clientData['client_id'];
        $this->members[$index]['is_client'] = true;
        $this->members[$index]['client_type'] = $clientType;
        $this->members[$index]['read_only_fields'] = $this->getReadOnlyFields($clientType);
        $this->members[$index]['last_name'] = $clientData['member']['last_name'];
        $this->members[$index]['first_name'] = $clientData['member']['first_name'];
        $this->members[$index]['middle_name'] = $clientData['member']['middle_name'] ?? '';
        $this->members[$index]['suffix'] = $clientData['member']['suffix'] ?? '';

        $birthdateValue = $clientData['birthdate'] ?? '';

        if ($clientType === 'PATIENT' && $birthdateValue === null) {
            $birthdateValue = '';
        }

        $this->members[$index]['birthdate'] = $birthdateValue;
        $this->members[$index]['age'] = $clientData['age'] ?? '';
        $this->members[$index]['civil_status'] = $clientData['civil_status'] ?? '';
        $this->members[$index]['occupation'] = $clientData['occupation'] ?? '';
        $this->members[$index]['monthly_income'] = (int) Str::replace(',', '', $clientData['monthly_income'] ?? 0);
    }

    protected function dehydrateMonthlyIncome(mixed $value, int $index): string|int|null
    {
        $income = data_get($this->members, "$index.monthly_income");

        if (empty($income)) {
            return null;
        }

        $cleanedIncome = (int) Str::of($income)->replaceMatches('/[^0-9]/', '');

        return $cleanedIncome;
    }

    protected function hydrateMonthlyIncome(mixed $value, int $index): ?string
    {
        $income = data_get($this->members, "$index.monthly_income");

        if (is_numeric($income) && $income > 0) {
            return Number::format($income);
        }

        return $income;
    }

    public function update()
    {
        $householdId = $this->household->household_id;

        foreach ($this->members as $index => $memberData) {
            if (isset($memberData['monthly_income'])) {
                $this->members[$index]['monthly_income'] = Str::replace(',', '', $memberData['monthly_income']);
            }
        }

        $validated = $this->validate();

        DB::transaction(function () use ($validated, $householdId) {
            $existingClientIds = Household::query()->where('household_id', $householdId)->pluck('client_id')->toArray();
            $updatedClientIds = [];

            foreach ($validated['members'] as $memberData) {
                $isNewPureMember = ! $memberData['is_client'];
                $readOnlyFields = $this->getReadOnlyFields($memberData['client_type'] ?? 'HOUSEHOLD_MEMBER');

                $clientId = $memberData['client_id'] ?? Account::generateId('CLIENT');

                $member = Member::updateOrCreate(
                    [
                        'member_id' => $clientId,
                    ],
                    [
                        'last_name' => $memberData['last_name'],
                        'first_name' => $memberData['first_name'],
                        'middle_name' => $memberData['middle_name'] ?? null,
                        'suffix' => $memberData['suffix'] ?? null,
                    ]
                );

                $clientData = [
                    'member_id' => $member->member_id,
                ];

                if ($isNewPureMember || ! collect($readOnlyFields)->contains('birthdate')) {
                    $clientData['birthdate'] = $memberData['birthdate'] ?? null;
                }

                if ($isNewPureMember || ! collect($readOnlyFields)->contains('civil_status')) {
                    $clientData['civil_status'] = $memberData['civil_status'] ?? null;
                }

                if ($isNewPureMember || ! collect($readOnlyFields)->contains('occupation')) {
                    $occupation = Occupation::where('occupation', $memberData['occupation'])->first();
                    $clientData['occupation_id'] = $occupation ? $occupation->occupation_id : null;
                }

                if ($isNewPureMember || ! collect($readOnlyFields)->contains('monthly_income')) {
                    $clientData['monthly_income'] = (int) ($memberData['monthly_income'] ?? 0) ?: null;
                }

                $client = Client::updateOrCreate(
                    ['client_id' => $clientId],
                    $clientData
                );

                Household::updateOrCreate(
                    [
                        'household_id' => $householdId,
                        'client_id' => $clientId,
                    ],
                    [
                        'household_name' => $validated['household_name'],
                        'educational_attainment' => $memberData['education'] ?? null,
                        'relationship_to_applicant' => $memberData['relation_to_head'] ?? null,
                    ]
                );

                $updatedClientIds[] = $clientId;
            }

            $clientsToRemove = collect($existingClientIds)->diff($updatedClientIds)->values()->all();

            if (! empty($clientsToRemove)) {
                Household::query()->where('household_id', $householdId)->whereIn('client_id', $clientsToRemove)->delete();
            }
        });

        Session::flash('success', 'Household has been updated successfully.');
        $this->dispatch('household-updated');

        return redirect()->route('profiles.households.list');
    }

    public function render()
    {
        return view('livewire.client.household-profile');
    }
}
