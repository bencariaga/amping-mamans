<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Audit\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the logs.
     */
    public function index()
    {
        $logs = Log::orderBy('happened_at', 'desc')->paginate(15);
        return view('pages.dashboard.logs.index', compact('logs'));
    }
}