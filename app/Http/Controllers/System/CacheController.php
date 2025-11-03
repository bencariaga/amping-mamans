<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class CacheController extends Controller
{
    public function clearCache(): RedirectResponse
    {
        try {
            Artisan::call('optimize:clear');
            Artisan::call('debugbar:clear');

            return redirect()->route('dashboard')->with('success', 'Cache cleared successfully!');
        } catch (Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Failed to clear cache: '.$e->getMessage());
        }
    }
}
