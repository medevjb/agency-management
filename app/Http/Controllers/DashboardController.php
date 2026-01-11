<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        // Only allow admin, operation manager, and project manager to view dashboard
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            // Redirect team users to orders dashboard
            return redirect()->route('orders.dashboard');
        }

        // You can fetch real data here when your application has models
        // For now, we'll return the view with sample data
        return view('dashboard');
    }
}
