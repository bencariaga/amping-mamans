<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Authentication\Role;
use App\Models\Communication\MessageTemplate;
use App\Models\Operation\Application;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;
use App\Models\User\Applicant;
use App\Models\User\Client;
use App\Models\User\Household;
use App\Models\User\Member;
use App\Models\User\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function listUsers(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'latest');
        $perPage = $request->input('per_page', 4);

        $baseQuery = Member::with(['profilePictures', 'staff.role'])->whereHas('staff');

        if ($search) {
            $term = "%{$search}%";
            $baseQuery->where(function ($q) use ($term) {
                $q->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", [$term])->orWhereRaw("CONCAT(last_name, ' ', COALESCE(middle_name,''), ' ', first_name) LIKE ?", [$term])->orWhereHas('staff.role', function ($qr) use ($term) {
                    $qr->where('role', 'like', $term);
                });
            });
        }

 

 

        $sortable = (new Collection(['oldest', 'last_name_asc', 'role_asc']))->implode(',');
        $baseQuery->when($sortBy === 'oldest', fn ($q) => $q->orderBy('member_id', 'asc'))->when($sortBy === 'last_name_asc', fn ($q) => $q->orderBy('last_name', 'asc'))->when($sortBy === 'role_asc', fn ($q) => $q->leftJoin('staff', 'members.member_id', '=', 'staff.member_id')->leftJoin('roles', 'staff.role_id', '=', 'roles.role_id')->orderBy('roles.role', 'asc')->select('members.*'))->when(! Str::contains($sortable, $sortBy), fn ($q) => $q->orderBy('member_id', 'desc'));
        $users = $perPage === 'all' ? $baseQuery->get() : $baseQuery->paginate($perPage);
        $roles = Role::all();

        return view('pages.sidebar.profiles.list.user-list', ['users' => $users, 'roles' => $roles]);
    }

    public function listClients(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'latest');
        $perPage = $request->input('per_page', 5);

        // Fetch ALL applicants regardless of year - no restrictions
        $baseQuery = Client::with(['member', 'occupation', 'contacts', 'applicant'])
            ->whereHas('applicant');

        if ($search) {
            $term = "%{$search}%";
            $baseQuery->where(function ($query) use ($term) {
                $query->whereHas('member', function ($q) use ($term) {
                    $q->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", [$term])->orWhereRaw("CONCAT(last_name, ' ', COALESCE(middle_name,''), ' ', first_name) LIKE ?", [$term]);
                })->orWhereHas('contacts', function ($q) use ($term) {
                    $q->where('phone_number', 'like', $term);
                })->orWhereHas('occupation', function ($q) use ($term) {
                    $q->where('occupation', 'like', $term);
                })->orWhere('monthly_income', 'like', $term);
            });
        }

        $baseQuery->when($sortBy === 'oldest', fn ($q) => $q->orderBy('client_id', 'asc'))->when($sortBy === 'last_name_asc', fn ($q) => $q->join('members', 'clients.member_id', '=', 'members.member_id')->orderBy('members.last_name', 'asc')->select('clients.*'))->when(! Arr::has(['oldest', 'last_name_asc'], $sortBy), fn ($q) => $q->orderBy('client_id', 'desc'));
        $clients = $perPage === 'all' ? $baseQuery->get() : $baseQuery->paginate($perPage);

        return view('pages.sidebar.profiles.list.applicant-list', ['clients' => $clients]);
    }

    public function listHouseholds(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'latest');
        $perPage = $request->input('per_page', 5);

        $baseQuery = Household::with(['client.member']);

        if ($search) {
            $term = "%{$search}%";
            $baseQuery->where(function ($query) use ($term) {
                $query->where('household_name', 'like', $term)
                    ->orWhereHas('client.member', function ($q) use ($term) {
                        $q->where('first_name', 'like', $term)
                            ->orWhere('middle_name', 'like', $term)
                            ->orWhere('last_name', 'like', $term)
                            ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", [$term]);
                    });
            });
        }

        $baseQuery->when($sortBy === 'oldest', fn ($q) => $q->orderBy('household_id', 'asc'))
            ->when($sortBy === 'last_name_asc', fn ($q) => $q->join('clients', 'households.client_id', '=', 'clients.client_id')
                ->join('members', 'clients.member_id', '=', 'members.member_id')
                ->orderBy('members.last_name', 'asc')
                ->select('households.*'))
            ->when(! Collection::make(['oldest', 'last_name_asc'])->contains($sortBy), fn ($q) => $q->orderBy('household_id', 'desc'));

        $households = $perPage === 'all' ? $baseQuery->get() : $baseQuery->paginate($perPage);

        return view('pages.sidebar.profiles.list.household-list', ['households' => $households]);
    }

    public function listTariffs(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'latest');
        $perPage = $request->input('per_page', 4);

        $baseQuery = TariffList::with('data');

        if ($search) {
            $term = "%{$search}%";
            $baseQuery->where(function ($q) use ($term) {
                $q->where('tariff_list_id', 'like', $term)
                    ->orWhere('effectivity_date', 'like', $term)
                    ->orWhereHas('data', function ($query) use ($term) {
                        $query->where('created_at', 'like', $term);
                    });
            });
        }

        $baseQuery->when($sortBy === 'oldest', fn ($q) => $q->orderBy('tariff_list_id', 'asc'))
            ->when($sortBy === 'latest', fn ($q) => $q->orderBy('tariff_list_id', 'desc'))
            ->when(! Collection::make(['oldest', 'latest'])->contains($sortBy), fn ($q) => $q->orderBy('tariff_list_id', 'desc'));

        if ($perPage === 'all') {
            $tariffModels = $baseQuery->get();
        } else {
            $perPage = (int) $perPage;
            $tariffModels = $baseQuery->paginate($perPage)->withQueryString();
        }

        $groupedTariffs = [];

        foreach ($tariffModels as $tariffModel) {
            $servicesList = ExpenseRange::where('tariff_list_id', $tariffModel->tariff_list_id)
                ->join('services', 'expense_ranges.service_id', '=', 'services.service_id')
                ->pluck('services.service_type')
                ->unique();
            $groupedTariffs[$tariffModel->data_id] = $servicesList;
        }

        return view('pages.dashboard.landing.tariff-lists', [
            'groupedTariffs' => $groupedTariffs,
            'tariffModels' => $tariffModels,
            'services' => Service::all(),
        ]);
    }

    public function listSponsors(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $sortBy = $request->get('sort_by', 'latest');
        $search = $request->get('search', '');

        $query = Sponsor::select('sponsors.*', 'data.created_at', 'members.full_name')
            ->leftJoin('members', 'sponsors.member_id', '=', 'members.member_id')
            ->leftJoin('accounts', 'members.account_id', '=', 'accounts.account_id')
            ->leftJoin('data', 'accounts.data_id', '=', 'data.data_id')
            ->with(['member.account.data'])
            ->withSum(['budgetUpdates as total_amount_contributed' => function ($q) {
                $q->where('possessor', 'Sponsor')->where('reason', 'Sponsor Donation');
            }], 'amount_change');

        if (! empty($search)) {
            $term = "%{$search}%";
            $query->where(function ($q) use ($term) {
                $q->where(function ($subQuery) use ($term) {
                    $subQuery->where('members.full_name', 'like', $term)
                        ->orWhere('members.first_name', 'like', $term)
                        ->orWhere('members.last_name', 'like', $term);
                })
                    ->orWhere('sponsors.sponsor_type', 'like', $term)
                    ->orWhere('sponsors.designation', 'like', $term)
                    ->orWhereRaw('CAST((SELECT SUM(amount_change) FROM budget_updates WHERE sponsor_id = sponsors.sponsor_id AND possessor = "Sponsor" AND reason = "Sponsor Donation") AS CHAR) LIKE ?', [$term]);
            });
        }

        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('data.created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('members.full_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('members.full_name', 'desc');
                break;
            case 'type_asc':
                $query->orderBy('sponsor_type', 'asc');
                break;
            case 'type_desc':
                $query->orderBy('sponsor_type', 'desc');
                break;
            default:
                $query->orderBy('data.created_at', 'desc');
                break;
        }

        $sponsors = $perPage === 'all' ? $query->distinct()->get() : $query->distinct()->paginate($perPage);

        return view('pages.sidebar.contribution.contributors')->with('sponsors', $sponsors);
    }

    public function listApplications(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $sortBy = $request->get('sort_by', 'latest');
        $search = $request->get('search', '');

        $query = Application::with([
            'applicant.client.member',
            'affiliatePartner',
            'expenseRange.service',
        ]);

        if (! empty($search)) {
            $term = "%{$search}%";
            $query->where(function ($q) use ($term) {
                $q->where('application_id', 'like', $term)
                    ->orWhereHas('applicant.client.member', function ($q) use ($term) {
                        $q->where('first_name', 'like', $term)
                            ->orWhere('middle_name', 'like', $term)
                            ->orWhere('last_name', 'like', $term)
                            ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", [$term]);
                    })
                    ->orWhereHas('affiliatePartner', function ($q) use ($term) {
                        $q->where('affiliate_partner_name', 'like', $term);
                    })
                    ->orWhereHas('expenseRange.service', function ($q) use ($term) {
                        $q->where('service_type', 'like', $term);
                    })
                    ->orWhereRaw('CAST(billed_amount AS CHAR) LIKE ?', [$term])
                    ->orWhereRaw('CAST(assistance_amount AS CHAR) LIKE ?', [$term]);
            });
        }

        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('application_id', 'asc');
                break;
            case 'applicant_asc':
                $query->join('applicants', 'applications.applicant_id', '=', 'applicants.applicant_id')
                    ->join('clients', 'applicants.client_id', '=', 'clients.client_id')
                    ->join('members', 'clients.member_id', '=', 'members.member_id')
                    ->orderBy('members.last_name', 'asc')
                    ->orderBy('members.first_name', 'asc')
                    ->select('applications.*');
                break;
            case 'applicant_desc':
                $query->join('applicants', 'applications.applicant_id', '=', 'applicants.applicant_id')
                    ->join('clients', 'applicants.client_id', '=', 'clients.client_id')
                    ->join('members', 'clients.member_id', '=', 'members.member_id')
                    ->orderBy('members.last_name', 'desc')
                    ->orderBy('members.first_name', 'desc')
                    ->select('applications.*');
                break;
            case 'amount_asc':
                $query->orderBy('billed_amount', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('billed_amount', 'desc');
                break;
            default:
                $query->orderBy('application_id', 'desc');
                break;
        }

        $applications = $perPage === 'all' ? $query->get() : $query->paginate($perPage);

        return view('pages.dashboard.landing.application-list', ['applications' => $applications]);
    }

    public function listSmsTemplates(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'latest');
        $perPage = $request->input('per_page', 5);

        $baseQuery = MessageTemplate::query();

        if ($search) {
            $term = "%{$search}%";
            $baseQuery->where(function ($query) use ($term) {
                $query->where('msg_tmp_title', 'like', $term)
                    ->orWhere('msg_tmp_text', 'like', $term);
            });
        }

        $baseQuery->when($sortBy === 'oldest', fn ($q) => $q->orderBy('msg_tmp_id', 'asc'))
            ->when($sortBy === 'title_asc', fn ($q) => $q->orderBy('msg_tmp_title', 'asc'))
            ->when($sortBy === 'title_desc', fn ($q) => $q->orderBy('msg_tmp_title', 'desc'))
            ->when(! Arr::has(['oldest', 'title_asc', 'title_desc'], $sortBy), fn ($q) => $q->orderBy('msg_tmp_id', 'desc'));

        $templates = $perPage === 'all' ? $baseQuery->get() : $baseQuery->paginate($perPage);

        return view('pages.dashboard.templates.text-messages.list', ['templates' => $templates]);
    }

    /**
     * Search existing applicants by name (JSON).
     * Query param: name
     * Returns: [{applicant_id, full_name, birthdate, phone_number}]
     * 
     * IMPORTANT: This searches ALL applicants from ALL years.
     * NO role restrictions - both Program Head and Encoder can access.
     * NO year filters - old applicants from 2004, 2020, etc. are included.
     * NO account status filters - searches all active applicants.
     */
    public function searchApplicants(Request $request)
    {
        $name = trim((string) $request->get('name', ''));

        if ($name === '') {
            return response()->json(['items' => []]);
        }

        $term = "%{$name}%";

        // Search ALL applicants regardless of year or role
        $results = Applicant::query()
            ->join('clients', 'applicants.client_id', '=', 'clients.client_id')
            ->join('members', 'clients.member_id', '=', 'members.member_id')
            ->leftJoin('contacts', function ($q) {
                $q->on('clients.client_id', '=', 'contacts.client_id')
                  ->where('contacts.contact_type', '=', 'Application');
            })
            ->where(function ($q) use ($term) {
                $q->where('members.first_name', 'like', $term)
                  ->orWhere('members.middle_name', 'like', $term)
                  ->orWhere('members.last_name', 'like', $term)
                  ->orWhereRaw("CONCAT(members.first_name, ' ', COALESCE(members.middle_name,''), ' ', members.last_name) LIKE ?", [$term])
                  ->orWhereRaw("CONCAT(members.last_name, ' ', COALESCE(members.middle_name,''), ' ', members.first_name) LIKE ?", [$term]);
            })
            ->orderBy('members.last_name', 'asc')
            ->limit(10)
            ->get([
                'applicants.applicant_id',
                'members.full_name',
                'clients.birthdate',
                'contacts.phone_number',
            ]);

        return response()->json([
            'items' => $results->map(function ($r) {
                return [
                    'applicant_id' => $r->applicant_id,
                    'full_name' => $r->full_name,
                    'birthdate' => $r->birthdate,
                    'phone_number' => $r->phone_number,
                ];
            })->all(),
        ]);
    }

    /**
     * Get a single applicant details (JSON) by applicant_id.
     */
    public function getApplicant(string $applicant)
    {
        $row = Applicant::query()
            ->join('clients', 'applicants.client_id', '=', 'clients.client_id')
            ->join('members', 'clients.member_id', '=', 'members.member_id')
            ->leftJoin('contacts', function ($q) {
                $q->on('clients.client_id', '=', 'contacts.client_id')
                  ->where('contacts.contact_type', '=', 'Application');
            })
            ->leftJoin('occupations', 'clients.occupation_id', '=', 'occupations.occupation_id')
            ->where('applicants.applicant_id', $applicant)
            ->first([
                'applicants.*',
                'clients.birthdate',
                'clients.age',
                'clients.sex',
                'clients.civil_status',
                'clients.monthly_income',
                'clients.occupation_id',
                'members.first_name',
                'members.middle_name',
                'members.last_name',
                'members.suffix',
                'members.full_name',
                'contacts.phone_number',
                'occupations.occupation as occupation_name',
            ]);

        if (! $row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'applicant_id' => $row->applicant_id,
            'first_name' => $row->first_name,
            'middle_name' => $row->middle_name,
            'last_name' => $row->last_name,
            'suffix' => $row->suffix,
            'birthdate' => $row->birthdate,
            'age' => $row->age,
            'sex' => $row->sex,
            'civil_status' => $row->civil_status,
            'phone_number' => $row->phone_number,
            'occupation_id' => $row->occupation_id,
            'occupation_name' => $row->occupation_name,
            'monthly_income' => $row->monthly_income,
            'province' => $row->province,
            'city' => $row->city,
            'municipality' => $row->municipality,
            'barangay' => $row->barangay,
            'subdivision' => $row->subdivision,
            'purok' => $row->purok,
            'sitio' => $row->sitio,
            'street' => $row->street,
            'phase' => $row->phase,
            'block_number' => $row->block_number,
            'house_number' => $row->house_number,
            'job_status' => $row->job_status,
            'house_occup_status' => $row->house_occup_status,
            'lot_occup_status' => $row->lot_occup_status,
            'phic_affiliation' => $row->phic_affiliation,
            'phic_category' => $row->phic_category,
            'is_also_patient' => $row->is_also_patient,
            'patient_number' => $row->patient_number,
            'full_name' => $row->full_name,
        ]);
    }

    /**
     * List patients belonging to an applicant (JSON).
     * Returns ALL patients for the applicant, regardless of application status.
     */
    public function getApplicantPatients(string $applicant)
    {
        // Get all patients for this applicant
        $patients = Patient::query()
            ->where('applicant_id', $applicant)
            ->with([
                'client.member',
                'client.contact'
            ])
            ->orderBy('patient_id', 'asc')
            ->get();

        return response()->json([
            'items' => $patients->map(function ($patient) {
                $member = $patient->client->member ?? null;
                $contact = $patient->client->contact ?? null;
                
                return [
                    'patient_id' => $patient->patient_id,
                    'patient_number' => $patient->patient_number,
                    'full_name' => $member ? $member->full_name : 'N/A',
                    'first_name' => $member ? $member->first_name : 'N/A',
                    'middle_name' => $member ? $member->middle_name : '',
                    'last_name' => $member ? $member->last_name : 'N/A',
                    'suffix' => $member ? $member->suffix : '',
                    'birthdate' => $patient->client->birthdate ?? 'N/A',
                    'gender' => $patient->client->gender ?? 'N/A',
                    'phone_number' => $contact ? $contact->phone_number : 'N/A',
                    'category' => $patient->category ?? 'N/A',
                ];
            })->all(),
        ]);
    }
}
