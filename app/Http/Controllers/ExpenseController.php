<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        if (
            $request->filled("date_from") &&
            $request->filled("date_to") &&
            $request->date_from > $request->date_to
        ) {
            return redirect()
                ->route("expenses.index")
                ->with(
                    "error",
                    'Tanggal "Dari" tidak boleh lebih besar dari Tanggal "Sampai".',
                );
        }

        $title = 'Pengeluaran';
        $subtitle = 'Daftar Pengeluaran';

        $query = Expense::with(['user']);

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->latest('expense_date')->paginate(10)->withQueryString();
        $todayTotal = Expense::whereDate('expense_date', today())->sum('amount');
        $monthTotal = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        return view(
            "pages.expenses",
            compact(
                "title",
                "subtitle",
                "expenses",
                "todayTotal",
                "monthTotal",
            ),
        );
    }

    public function show(Expense $expense)
    {
        return response()->json([
            'success' => true,
            'expense' => [
                'id' => $expense->id,
                'amount' => $expense->amount,
                'description' => $expense->description,
                'expense_date' => $expense->expense_date->format('Y-m-d'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
            'expense_date' => 'required|date',
        ], [
            'amount.required' => 'Jumlah tidak boleh kosong',
            'amount.min' => 'Jumlah tidak boleh negatif',
            'description.required' => 'Deskripsi wajib diisi',
            'description.max' => 'Deskripsi maksimal 500 karakter',
            'expense_date.required' => 'Tanggal harus diisi',
        ]);

        Expense::create([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'description' => $request->description,
            'expense_date' => $request->expense_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil ditambahkan',
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
            'expense_date' => 'required|date',
        ], [
            'amount.required' => 'Jumlah tidak boleh kosong',
            'amount.min' => 'Jumlah tidak boleh negatif',
            'description.required' => 'Deskripsi wajib diisi',
            'description.max' => 'Deskripsi maksimal 500 karakter',
            'expense_date.required' => 'Tanggal harus diisi',
        ]);

        $expense->update([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'description' => $request->description,
            'expense_date' => $request->expense_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil diperbarui',
        ]);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dihapus',
        ]);
    }
}
