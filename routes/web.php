<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\LogoutController;
use App\Http\Controllers\Communication\MessageController;
use App\Http\Controllers\Communication\MessageTemplateController;
use App\Http\Controllers\Core\ApplicationController;
use App\Http\Controllers\Core\GLController;
use App\Http\Controllers\Core\OccupationController;
use App\Http\Controllers\Core\RoleController;
use App\Http\Controllers\Core\SearchController;
use App\Http\Controllers\Core\ServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Financial\AffiliatePartnerController;
use App\Http\Controllers\Financial\BudgetUpdateController;
use App\Http\Controllers\Financial\SponsorController;
use App\Http\Controllers\Financial\TariffListController;
use App\Http\Controllers\Profile\ClientProfileController;
use App\Http\Controllers\Profile\HouseholdProfileController;
use App\Http\Controllers\Profile\UserProfileController;
use App\Http\Controllers\Registration\ClientRegistrationController;
use App\Http\Controllers\Registration\HouseholdRegistrationController;
use App\Http\Controllers\Registration\UserRegistrationController;
use App\Http\Controllers\Reports\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/about', fn () => view('about'))->name('about');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    
    // Debug route to check applicant data access
    Route::get('/debug/applicants-check', function () {
        $user = auth()->user();
        $role = optional(optional($user->staff)->role)->role;
        
        $allApplicants = \App\Models\User\Applicant::with('client.member')->get();
        $applicantsByYear = \App\Models\User\Applicant::select(\DB::raw('SUBSTRING(applicant_id, 11, 4) as year, COUNT(*) as count'))
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();
        
        return response()->json([
            'current_user' => [
                'name' => $user->full_name,
                'role' => $role,
                'member_id' => $user->member_id,
                'account_id' => $user->account_id,
            ],
            'total_applicants' => $allApplicants->count(),
            'applicants_by_year' => $applicantsByYear,
            'sample_old_applicants' => $allApplicants->take(10)->map(function($app) {
                return [
                    'id' => $app->applicant_id,
                    'name' => optional($app->client->member)->full_name,
                    'year' => substr($app->applicant_id, 10, 4),
                ];
            }),
        ]);
    });

    Route::get('/tariff-lists', [SearchController::class, 'listTariffs'])->name('tariff-lists');
    Route::get('/request-service-assistance', [ApplicationController::class, 'showAssistanceRequest'])->name('request-service-assistance');
    Route::get('/guarantee-letter', [DashboardController::class, 'guaranteeLetter'])->name('guarantee-letter');

    Route::prefix('message-templates')->group(function () {
        Route::get('/', [SearchController::class, 'listSmsTemplates'])->name('message-templates.list');
        Route::get('/create', [MessageTemplateController::class, 'create'])->name('message-templates.create');
        Route::post('/', [MessageTemplateController::class, 'store'])->name('message-templates.store');
        Route::get('/{template}/edit', [MessageTemplateController::class, 'edit'])->name('message-templates.edit');
        Route::put('/{template}', [MessageTemplateController::class, 'update'])->name('message-templates.update');
        Route::delete('/{template}', [MessageTemplateController::class, 'destroy'])->name('message-templates.destroy');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{type}', [ReportController::class, 'show'])->name('show'); // types: applicants, patients, applications, tariffs
        Route::get('/{type}/pdf', [ReportController::class, 'downloadPdf'])->name('pdf');
        Route::get('/{type}/csv', [ReportController::class, 'downloadCsv'])->name('csv');
        Route::get('/{type}/pdf-dom', [ReportController::class, 'downloadPdfDompdf'])->name('pdf_dom');
    });


    Route::prefix('/messages')->name('messages.')->group(function () {
        Route::post('/send', [MessageController::class, 'sendMessage'])->name('send');
    });

    Route::prefix('profile/')->group(function () {
        Route::get('/', [UserProfileController::class, 'show'])->name('user.profile.show');
        Route::put('/', [UserProfileController::class, 'update'])->name('user.profile.update');
        Route::delete('/', [UserProfileController::class, 'destroy'])->name('user.profile.destroy');
    });

    Route::prefix('profiles')->name('profiles.')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [SearchController::class, 'listUsers'])->name('list');
            Route::put('roles', [RoleController::class, 'update'])->name('roles.update');
            Route::get('add', [UserRegistrationController::class, 'create'])->name('create');
            Route::post('/', [UserRegistrationController::class, 'store'])->name('store');
            Route::get('{user}', [UserProfileController::class, 'show'])->name('show');
            Route::put('{user}', [UserProfileController::class, 'update'])->name('update');
            Route::put('{user}/deactivate', [UserProfileController::class, 'deactivate'])->name('deactivate');
            Route::delete('{user}', [UserProfileController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('applicants')->name('applicants.')->group(function () {
            Route::get('/', [SearchController::class, 'listClients'])->name('list');
            Route::get('add', [ClientRegistrationController::class, 'create'])->name('create');
            Route::post('/', [ClientRegistrationController::class, 'store'])->name('store');
            Route::get('{applicant}', [ClientProfileController::class, 'show'])->name('show');
            Route::put('{applicant}', [ClientProfileController::class, 'update'])->name('update');
            Route::delete('{applicant}', [ClientProfileController::class, 'destroy'])->name('destroy');
            Route::post('{applicant}/patients', [ClientRegistrationController::class, 'storePatients'])->name('patients.store');
            Route::delete('{applicant}/patients/{patient}', [ClientRegistrationController::class, 'destroyPatient'])->name('patients.destroy');
            Route::get('{applicant}/search', [SearchController::class, 'searchApplicants'])->name('search');
        });

        Route::prefix('households')->name('households.')->group(function () {
            Route::get('/', [SearchController::class, 'listHouseholds'])->name('list');
            Route::get('add', [HouseholdRegistrationController::class, 'create'])->name('create');
            Route::post('/', [HouseholdRegistrationController::class, 'store'])->name('store');
            Route::get('{household}', [HouseholdProfileController::class, 'show'])->name('show');
            Route::put('{household}', [HouseholdProfileController::class, 'update'])->name('update');
            Route::delete('{household}', [HouseholdProfileController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('api')->group(function () {
        Route::get('/occupations', [OccupationController::class, 'index']);
        Route::get('/affiliate-partners', [AffiliatePartnerController::class, 'index']);
        Route::get('/sponsors', [SponsorController::class, 'index']);
        Route::get('/latest-budget', [BudgetUpdateController::class, 'getLatestBudget']);
        // Username validation
        Route::post('/validate-username', [UserRegistrationController::class, 'validateUsername'])->name('validate-username');
        // Applicants search (JSON)
        Route::get('/applicants/search', [SearchController::class, 'searchApplicants']);
        // Applicant details (JSON)
        Route::get('/applicants/{applicant}', [SearchController::class, 'getApplicant']);
        // Applicant patients (JSON)
        Route::get('/applicants/{applicant}/patients', [SearchController::class, 'getApplicantPatients']);
        // Patient details (JSON)
        Route::get('/patients/{patient}', [SearchController::class, 'getPatient']);
    });

    Route::prefix('api')->name('api.')->group(function () {
        Route::prefix('households')->name('households.')->group(function () {
            Route::get('search', [HouseholdProfileController::class, 'search'])->name('search');
            Route::post('verify-name', [HouseholdProfileController::class, 'verifyName'])->name('verify-name');
            Route::post('verify-full-name', [HouseholdProfileController::class, 'verifyFullName'])->name('verify-full-name');
        });
    });

    Route::prefix('/tariff-lists')->name('tariff-lists.')->group(function () {
        Route::get('/{tariff_list_id}/preview', [TariffListController::class, 'getTariffListPreview'])->name('preview');
        Route::get('/preview-id', [TariffListController::class, 'previewTariffId']);
        Route::get('/taken-dates', [TariffListController::class, 'getTakenDates'])->name('taken-dates');
        Route::get('/suggested-date', [TariffListController::class, 'getSuggestedEffectivityDate'])->name('suggested-date');
        Route::get('/check-date', [TariffListController::class, 'checkEffectivityDate'])->name('check-date');
        Route::get('/{tariffListId}/edit', [TariffListController::class, 'edit'])->name('edit');
        Route::post('/{tariffListId}/add-service', [TariffListController::class, 'addService'])->name('add-service');
        Route::post('/{tariffListId}/remove-service', [TariffListController::class, 'removeService'])->name('remove-service');
        Route::get('/{tariffListId}', [TariffListController::class, 'show'])->name('view');
        Route::get('/create', [TariffListController::class, 'create'])->name('create');
        Route::post('/', [TariffListController::class, 'store'])->name('store');
        Route::put('/{tariffListId}', [TariffListController::class, 'update'])->name('update');
        Route::delete('/{tariffListId}', [TariffListController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [SearchController::class, 'listApplications'])->name('list');
        Route::post('/store', [ApplicationController::class, 'store'])->name('store');
        Route::post('/verify-phone', [ApplicationController::class, 'verifyPhoneNumber'])->name('verify-phone');
        Route::post('/applicant-details', [ApplicationController::class, 'getApplicantDetails'])->name('applicant-details');
        Route::get('/calculate-amount', [ApplicationController::class, 'calculateAssistanceAmount'])->name('calculate-amount');
        Route::get('/affiliate-partners-by-service', [ApplicationController::class, 'getAffiliatePartnersByService'])->name('affiliate-partners-by-service');
        Route::get('/search-applicant', [ApplicationController::class, 'searchApplicant'])->name('search-applicant');
        Route::get('{application}/details', [ApplicationController::class, 'details'])->name('details');
        Route::get('{application}/guarantee-letter', [GLController::class, 'generatePDF'])->name('guarantee-letter.pdf');
        Route::delete('{application}/destroy', [ApplicationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/roles')->name('roles.')->group(function () {
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/occupations')->name('occupations.')->group(function () {
        Route::post('/confirm-changes', [OccupationController::class, 'confirmChanges'])->name('confirm-changes');
        Route::delete('/{id}', [OccupationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/services')->name('services.')->group(function () {
        Route::delete('/{id}', [ServiceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/affiliate-partners')->name('affiliate-partners.')->group(function () {
        Route::post('/confirm-changes', [AffiliatePartnerController::class, 'confirmChanges'])->name('confirm-changes');
        Route::delete('/{id}', [AffiliatePartnerController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/sponsors')->name('sponsors.')->group(function () {
        Route::get('/', [SearchController::class, 'listSponsors'])->name('list');
        Route::get('/table/{id}', [BudgetUpdateController::class, 'showContributionTable'])->name('tables.show');
        Route::get('/{id}', [SponsorController::class, 'show'])->name('show');
        Route::post('/confirm-changes', [SponsorController::class, 'confirmChanges'])->name('confirm-changes');
        Route::delete('/{id}', [SponsorController::class, 'destroy'])->name('destroy');
        Route::post('/contributions/update', [SponsorController::class, 'updateContributions'])->name('contributions.update');
        Route::delete('/contributions/{id}', [BudgetUpdateController::class, 'destroy'])->name('contributions.destroy');
    });
});
