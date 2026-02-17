<?php

namespace App\Http\Controllers;

class POSController extends Controller
{
    public function index()
    {
        $title = 'Transaksi';
        $subtitle = 'Catat transaksi lebih mudah';

        return view('pages.pos', compact('title', 'subtitle'));
    }
}
