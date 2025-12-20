<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // You can fetch real data here when your application has models
        // For now, we'll return the view with sample data
        return view('dashboard');
    }
}
