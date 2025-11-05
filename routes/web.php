<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\LogoutController;
use App\Http\Controllers\Communication\MessageController;
use App\Http\Controllers\Communication\MessageTemplateController;
use App\Http\Controllers\Core\AccountController;
use App\Http\Controllers\Core\ApplicationController;
use App\Http\Controllers\Core\ArchiveController;
use App\Http\Controllers\Core\AuditLogController;
use App\Http\Controllers\Core\GLController;
use App\Http\Controllers\Core\GLTemplateController;
use App\Http\Controllers\Core\MiscellaneousController;
use App\Http\Controllers\Core\OccupationController;
use App\Http\Controllers\Core\ReportController;
use App\Http\Controllers\Core\RoleController;
use App\Http\Controllers\Core\SearchController;
use App\Http\Controllers\Core\ServiceController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Financial\AffiliatePartnerController;
use App\Http\Controllers\Financial\BudgetUpdateController;
use App\Http\Controllers\Financial\SponsorController;
use App\Http\Controllers\Financial\TariffListController;
use App\Http\Controllers\Profile\ApplicantProfileController;
use App\Http\Controllers\Profile\HouseholdProfileController;
use App\Http\Controllers\Profile\UserProfileController;
use App\Http\Controllers\Registration\ApplicantRegistrationController;
use App\Http\Controllers\Registration\HouseholdRegistrationController;
use App\Http\Controllers\Registration\UserRegistrationController;
use App\Http\Controllers\System\CacheController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/about', fn () => view('about'))->name('about');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/deactivated-account', fn () => view('pages.authentication.deactivated-account'))->name('deactivated-account');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::post('/clear-cache', [CacheController::class, 'clearCache'])->name('clear-cache');
    Route::get('/tariff-lists', [SearchController::class, 'listTariffs'])->name('tariff-lists');
    Route::get('/request-assistance', [ApplicationController::class, 'showAssistanceRequest'])->name('request-assistance');
    Route::get('/guarantee-letter/{template?}', [GLTemplateController::class, 'viewGLTemplate'])->name('guarantee-letter');

    Route::prefix('profile/')->group(function () {
        Route::get('/', [UserProfileController::class, 'show'])->name('user.profile.show');
        Route::put('/', [UserProfileController::class, 'update'])->name('user.profile.update');
        Route::put('deactivate', [UserProfileController::class, 'updateDeactivate'])->name('user.profile.deactivate');
        Route::delete('/', [UserProfileController::class, 'destroy'])->name('user.profile.destroy');
    });

    Route::prefix('profiles')->name('profiles.')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [SearchController::class, 'listUsers'])->name('list');
            Route::put('roles', [RoleController::class, 'update'])->name('roles.update');
            Route::get('add', [UserRegistrationController::class, 'create'])->name('create');
            Route::post('/', [UserRegistrationController::class, 'store'])->name('store');
            Route::get('{staffId}', [UserProfileController::class, 'show'])->name('show');
            Route::put('{staffId}', [UserProfileController::class, 'update'])->name('update');
            Route::put('{staffId}/deactivate', [UserProfileController::class, 'updateDeactivate'])->name('deactivate');
            Route::delete('{staffId}', [UserProfileController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('applicants')->name('applicants.')->group(function () {
            Route::get('/', [SearchController::class, 'listClients'])->name('list');
            Route::get('add', [ApplicantRegistrationController::class, 'create'])->name('create');
            Route::post('/', [ApplicantRegistrationController::class, 'store'])->name('store');
            Route::get('{applicant}', [ApplicantProfileController::class, 'show'])->name('show');
            Route::put('{applicant}', [ApplicantProfileController::class, 'update'])->name('update');
            Route::delete('{applicant}', [ApplicantProfileController::class, 'destroy'])->name('destroy');
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

    Route::prefix('/tariff-lists')->name('tariff-lists.')->group(function () {
        Route::get('/{tariff_list_id}/preview', [TariffListController::class, 'getTariffListPreview'])->name('preview');
        Route::get('/preview-id', [TariffListController::class, 'previewTariffId']);
        Route::get('/taken-dates', [TariffListController::class, 'getTakenDates'])->name('taken-dates');
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

    Route::prefix('gl-templates')->name('gl-templates.')->group(function () {
        Route::get('/', [GLTemplateController::class, 'listGLTemplates'])->name('list');
        Route::get('/create', [GLTemplateController::class, 'createGLTemplate'])->name('create');
        Route::post('/', [GLTemplateController::class, 'storeGLTemplate'])->name('store');
        Route::get('/{template}/edit', [GLTemplateController::class, 'editGLTemplate'])->name('edit');
        Route::put('/{template}', [GLTemplateController::class, 'updateGLTemplate'])->name('update');
        Route::delete('/{template}', [GLTemplateController::class, 'destroyGLTemplate'])->name('destroy');
    });

    Route::prefix('message-templates')->group(function () {
        Route::get('/', [SearchController::class, 'listSmsTemplates'])->name('message-templates.list');
        Route::get('/create', [MessageTemplateController::class, 'create'])->name('message-templates.create');
        Route::post('/', [MessageTemplateController::class, 'store'])->name('message-templates.store');
        Route::get('/{template}/edit', [MessageTemplateController::class, 'edit'])->name('message-templates.edit');
        Route::put('/{template}', [MessageTemplateController::class, 'update'])->name('message-templates.update');
        Route::delete('/{template}', [MessageTemplateController::class, 'destroy'])->name('message-templates.destroy');
    });

    Route::prefix('/messages')->name('messages.')->group(function () {
        Route::post('/send', [MessageController::class, 'sendMessage'])->name('send');
    });

    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [SearchController::class, 'listApplications'])->name('list');
        Route::post('/store', [ApplicationController::class, 'store'])->name('store');
        Route::post('/verify-phone', [ApplicationController::class, 'verifyPhoneNumber'])->name('verify-phone');
        Route::post('/applicant-details', [ApplicationController::class, 'getApplicantDetails'])->name('applicant-details');
        Route::get('/calculate-amount', [ApplicationController::class, 'calculateAssistanceAmount'])->name('calculate-amount');
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
        Route::post('/sponsorships/update', [SponsorController::class, 'updateContributions'])->name('contributions.update');
        Route::delete('/sponsorships/{id}', [BudgetUpdateController::class, 'destroy'])->name('contributions.destroy');
    });

    Route::prefix('miscellaneous')->name('miscellaneous.')->group(function () {
        Route::get('/', [MiscellaneousController::class, 'index'])->name('index');

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::post('/', [MiscellaneousController::class, 'storeRole'])->name('store');
            Route::put('{id}', [MiscellaneousController::class, 'updateRole'])->name('update');
            Route::delete('{id}', [MiscellaneousController::class, 'destroyRole'])->name('destroy');
        });

        Route::prefix('occupations')->name('occupations.')->group(function () {
            Route::post('/', [MiscellaneousController::class, 'storeOccupation'])->name('store');
            Route::put('{id}', [MiscellaneousController::class, 'updateOccupation'])->name('update');
            Route::delete('{id}', [MiscellaneousController::class, 'destroyOccupation'])->name('destroy');
        });

        Route::prefix('services')->name('services.')->group(function () {
            Route::post('/', [MiscellaneousController::class, 'storeService'])->name('store');
            Route::put('{id}', [MiscellaneousController::class, 'updateService'])->name('update');
            Route::delete('{id}', [MiscellaneousController::class, 'destroyService'])->name('destroy');
        });

        Route::prefix('households')->name('households.')->group(function () {
            Route::post('/', [MiscellaneousController::class, 'storeHousehold'])->name('store');
            Route::put('{id}', [MiscellaneousController::class, 'updateHousehold'])->name('update');
            Route::delete('{id}', [MiscellaneousController::class, 'destroyHousehold'])->name('destroy');
        });
    });

    Route::prefix('budget-updates')->name('budget-updates.')->group(function () {
        Route::post('/', [BudgetUpdateController::class, 'store'])->name('store');
        Route::post('/supplementary', [BudgetUpdateController::class, 'createSupplementaryBudget'])->name('supplementary');
        Route::post('/apply-changes', [BudgetUpdateController::class, 'applyChanges'])->name('apply-changes');
    });

    Route::prefix('api')->group(function () {
        Route::get('/occupations', [OccupationController::class, 'index']);
        Route::get('/affiliate-partners', [AffiliatePartnerController::class, 'index']);
        Route::get('/sponsors', [SponsorController::class, 'index']);
        Route::get('/latest-budget', [BudgetUpdateController::class, 'getLatestBudget']);
    });

    Route::prefix('api')->name('api.')->group(function () {
        Route::prefix('households')->name('households.')->group(function () {
            Route::get('search', [HouseholdProfileController::class, 'search'])->name('search');
            Route::post('verify-name', [HouseholdProfileController::class, 'verifyName'])->name('verify-name');
            Route::post('verify-full-name', [HouseholdProfileController::class, 'verifyFullName'])->name('verify-full-name');
        });
    });

    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/deactivated', [AccountController::class, 'deactivated'])->name('deactivated');
        Route::put('{accountId}/reactivate', [AccountController::class, 'reactivate'])->name('reactivate');
        Route::delete('{accountId}', [AccountController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('archives')->name('archives.')->group(function () {
        Route::get('/', [ArchiveController::class, 'index'])->name('list');
        Route::put('{id}/unarchive', [ArchiveController::class, 'unarchive'])->name('unarchive');
        Route::delete('{id}', [ArchiveController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('list');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{type}', [ReportController::class, 'show'])->name('show');
        Route::get('/{type}/pdf', [ReportController::class, 'downloadPdf'])->name('pdf');
        Route::get('/{type}/xlsx', [ReportController::class, 'downloadXlsx'])->name('xlsx');
        Route::get('/{type}/pdf-dom', [ReportController::class, 'downloadPdfDompdf'])->name('pdf_dom');
    });
});
