<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {

        $title = 'Pelanggan';
        $subtitle = 'Manajemen Data Pelanggan';
        $customers = Customer::orderBy('name')->paginate(10);

        return view('pages.customers', compact('title', 'subtitle', 'customers'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isOwner()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama pelanggan wajib diisi',
        ]);

        Customer::create($request->only('name'));

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil ditambahkan!'
        ]);
    }

     public function show(Customer $customer)
    {
        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        if (!auth()->user()->isOwner()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $customer->update($request->only('name'));

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil diperbarui!'
        ]);
    }

    public function destroy(Customer $customer)
    {
        if (!auth()->user()->isOwner()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil dihapus!'
        ]);
    }
}
