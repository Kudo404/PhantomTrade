<?php

namespace App\Http\Controllers;

class LicenseController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        // Only Ghost Hunters with a license can view this
        if (! $user->isGhostHunter() || ! $user->license_number) {
            abort(403, 'You do not have a Hunter License.');
        }

        return view('license.index', compact('user'));
    }
}