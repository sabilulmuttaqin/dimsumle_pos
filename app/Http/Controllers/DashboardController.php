<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $title = "Dashboard";
        $subtitle = "Ringkasan performa hari ini";

        return view(
            "pages.dashboard",
            compact(
                "title",
                "subtitle",
            ),
        );
    }
}
