<?php

namespace App\Providers;

use App\Models\Authentication\Account;
use App\Models\Authentication\Occupation;
use App\Models\Authentication\Role;
use App\Models\Communication\Message;
use App\Models\Communication\MessageTemplate;
use App\Models\Operation\Application;
use App\Models\Operation\BudgetUpdate;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\GuaranteeLetter;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;
use App\Models\User\AffiliatePartner;
use App\Models\User\Applicant;
use App\Models\User\Client;
use App\Models\User\Contact;
use App\Models\User\Household;
use App\Models\User\Member;
use App\Models\User\Patient;
use App\Models\User\Signer;
use App\Models\User\Sponsor;
use App\Models\User\Staff;

use Illuminate\Support\ServiceProvider;
use App\Observers\ModelObserver;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Account::observe(ModelObserver::class);
        Occupation::observe(ModelObserver::class);
        Role::observe(ModelObserver::class);
        Message::observe(ModelObserver::class);
        MessageTemplate::observe(ModelObserver::class);
        Application::observe(ModelObserver::class);
        BudgetUpdate::observe(ModelObserver::class);
        ExpenseRange::observe(ModelObserver::class);
        GuaranteeLetter::observe(ModelObserver::class);
        Service::observe(ModelObserver::class);
        TariffList::observe(ModelObserver::class);
        AffiliatePartner::observe(ModelObserver::class);
        Applicant::observe(ModelObserver::class);
        Client::observe(ModelObserver::class);
        Contact::observe(ModelObserver::class);
        Household::observe(ModelObserver::class);
        Member::observe(ModelObserver::class);
        Patient::observe(ModelObserver::class);
        Signer::observe(ModelObserver::class);
        Sponsor::observe(ModelObserver::class);
        Staff::observe(ModelObserver::class);
        // Register any other models you want to log here.
    }
}