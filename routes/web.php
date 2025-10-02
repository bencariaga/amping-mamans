<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Financial\BudgetUpdateController;
use App\Http\Controllers\Financial\AffiliatePartnerController;
use App\Http\Controllers\Financial\SponsorController;
use App\Http\Controllers\Core\ApplicationController;
use App\Http\Controllers\Core\GLController;
use App\Http\Controllers\Core\ServiceController;
use App\Http\Controllers\Core\OccupationController;
use App\Http\Controllers\Core\RoleController;
use App\Http\Controllers\Core\SearchController;
use App\Http\Controllers\Financial\TariffListController;
use App\Http\Controllers\Profile\UserProfileController;
use App\Http\Controllers\Profile\ClientProfileController;
use App\Http\Controllers\Registration\UserRegistrationController;
use App\Http\Controllers\Registration\ClientRegistrationController;
use App\Http\Controllers\Communication\MessageTemplateController;
use App\Http\Controllers\Core\ReportController;


Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/about', fn() => view('about'))->name('about');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::get('/guarantee-letter', [DashboardController::class, 'guaranteeLetter'])->name('guarantee-letter');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    Route::prefix('profile/')->group(function () {
        Route::get('/', [UserProfileController::class, 'show'])->name('user.profile.show');
        Route::put('/', [UserProfileController::class, 'update'])->name('user.profile.update');
        Route::delete('/', [UserProfileController::class, 'destroy'])->name('user.profile.destroy');
    });

    // SMS template
    Route::prefix('message-templates')->group(function () {
        Route::get('/', [MessageTemplateController::class, 'index'])->name('message-templates.index');
        Route::get('/create', [MessageTemplateController::class, 'create'])->name('message-templates.create');
        Route::post('/', [MessageTemplateController::class, 'store'])->name('message-templates.store');
        Route::get('/{id}/edit', [MessageTemplateController::class, 'edit'])->name('message-templates.edit');
        Route::put('/{id}', [MessageTemplateController::class, 'update'])->name('message-templates.update');
        Route::delete('/{id}', [MessageTemplateController::class, 'destroy'])->name('message-templates.destroy');
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
        });
    });

    // Route::prefix('applications')->name('applications.')->group(function () {
    //     Route::get('/', [SearchController::class, 'listApplications'])->name('list');
    //     Route::get('/assistance-request', [ApplicationController::class, 'showAssistanceRequest'])->name('assistance-request');
    //     Route::post('/store', [ApplicationController::class, 'store'])->name('store');
    //     Route::post('/verify-phone', [ApplicationController::class, 'verifyPhoneNumber'])->name('verify-phone');
    //     Route::get('/calculate-amount', [ApplicationController::class, 'calculateAssistanceAmount'])->name('calculate-amount');
    // });

    Route::prefix('/tariff-lists')->name('tariff-lists.')->group(function () {
        Route::get('/', [TariffListController::class, 'showTariffListVersions'])->name('rows.show');
        Route::get('/{tariff_list_id}/preview', [TariffListController::class, 'getTariffListPreview'])->name('preview');
        Route::get('/create', [TariffListController::class, 'create'])->name('create');
        Route::post('/', [TariffListController::class, 'store'])->name('store');
        Route::put('/update', [TariffListController::class, 'updateTariffLists'])->name('update');
        Route::delete('/{tariff_list_id}', [TariffListController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('api')->group(function () {
        Route::get('/occupations', [OccupationController::class, 'index']);
        Route::get('/affiliate-partners', [AffiliatePartnerController::class, 'index']);
        Route::get('/sponsors', [SponsorController::class, 'index']);
        Route::get('/latest-budget', [BudgetUpdateController::class, 'getLatestBudget']);
    });

    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [SearchController::class, 'listApplications'])->name('list');
        Route::get('/assistance-request', [ApplicationController::class, 'showAssistanceRequest'])->name('assistance-request');
        Route::post('/store', [ApplicationController::class, 'store'])->name('store');
        Route::post('/verify-phone', [ApplicationController::class, 'verifyPhoneNumber'])->name('verify-phone');
        Route::get('/calculate-amount', [ApplicationController::class, 'calculateAssistanceAmount'])->name('calculate-amount');
        Route::get('{application}/details', [ApplicationController::class, 'details'])->name('details');
        Route::post('{application}/authorize', [GLController::class, 'authorizeApplication'])->name('authorize');
        Route::post('{application}/reject', [GLController::class, 'reject'])->name('reject');
        Route::get('{application}/guarantee-letter', [GLController::class, 'generatePDF'])->name('guarantee-letter.pdf');

        Route::get('/search-applicant', [ApplicationController::class, 'searchApplicant'])->name('search-applicant');
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

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/assistance-requests', [ReportController::class, 'assistanceRequests'])->name('assistance-requests');
        Route::get('/assistance-requests/pdf', [ReportController::class, 'assistanceRequestsPdf'])->name('assistance-requests.pdf');
    });
});
