<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operation\Application;
use App\Models\User\Applicant;
use App\Models\Operation\BudgetUpdate;

class ArchiveController extends Controller
{   

    public function index()
    {
        return view('pages.dashboard.archive.index');
    }

    public function applications()
    {
        $applications = Application::where('is_archived', 1)->with('applicant.client.member')->get();
        return view('pages.dashboard.archive.applications-index', compact('applications'));
    }

    public function applicants()
    {
        $applicants = Applicant::where('is_archived', 1)->with('client.member')->get();
        return view('pages.dashboard.archive.applicants-index', compact('applicants'));
    }

    public function budgetUpdates()
    {
        $budgetUpdates = BudgetUpdate::where('is_archived', 1)->get();
        return view('pages.dashboard.archive.budget-updates-index', compact('budgetUpdates'));
    }

    public function bulkUnarchive(Request $request, $model)
    {
        $ids = $request->input('ids', []);
        $modelClass = [
            'applications' => Application::class,
            'applicants' => Applicant::class,
            'budget-updates' => BudgetUpdate::class,
        ][$model] ?? null;

        if (!$modelClass) {
            return redirect()->back()->with('error', 'Invalid model.');
        }

        $modelClass::whereIn($modelClass::getPrimaryKey(), $ids)->update(['is_archived' => false]);

        return redirect()->back()->with('success', 'Selected records have been unarchived.');
    }
}