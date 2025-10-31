<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Core\SearchController;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SidebarController extends Controller
{
    public function usersList(Request $request)
    {
        return app(SearchController::class)->listUsers($request);
    }

    public function clientsList(Request $request)
    {
        return app(SearchController::class)->listClients($request);
    }

    public function assistanceRequest(): View
    {
        return view('pages.sidebar.application-entry.request-service-assistance');
    }

    public function users(): View
    {
        return view('pages.sidebar.profiles.profile.users');
    }

    public function registerUser(): View
    {
        return view('pages.sidebar.profiles.register.user');
    }

    /*

    public function archives(): View
    {
        return view('pages.sidebar.system.archives');
    }

    public function deactivatedAccounts(): View
    {
        return view('pages.sidebar.system.deactivated-accounts');
    }

    public function logs(): View
    {
        return view('pages.sidebar.system.logs');
    }

    public function reports(): View
    {
        return view('pages.sidebar.system.reports');
    }

    */
}
