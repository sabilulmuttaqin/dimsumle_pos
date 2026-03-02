<?php

namespace App\Http\Controllers;

use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $title = "Dashboard";
        $subtitle = "Ringkasan performa hari ini";
        $lowStockProducts = Product::where("stock", "<=", 30)->count();
        return view(
            "pages.dashboard",
            compact(
                "lowStockProducts",
                "title",
                "subtitle",
            ),
        );
    }
}
