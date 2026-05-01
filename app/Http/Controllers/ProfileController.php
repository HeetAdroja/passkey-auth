<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile');
    }

    public function clearcache()
    {
        Artisan::call('cache:clear');
        log::info('Cache cleared by user ID: ' . auth()->id());
        return redirect()->route('profile')->with('cache_cleared', 'Cache cleared!');
    }
}
