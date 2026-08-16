<?php

namespace App\Http\Controllers;

use App\Support\GlobalMonth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SelectedMonthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Set the month every dashboard reads from and return to wherever the
     * picker was used from.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        GlobalMonth::set($validated['month']);

        return back();
    }
}
