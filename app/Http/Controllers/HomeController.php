<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * The root URL. Shows the landing page for guests,
     * the dashboard for logged-in users.
     */
    public function index()
    {
        if (auth()->check()) {
            return app(DashboardController::class)->index(request());
        }

        return view('landing');
    }
}