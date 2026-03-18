<?php

namespace App\Http\Controllers;

use App\Models\POS;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $title = "History";
        $subtitle = "Riwayat transaksi";
        $user = auth()->user();

        $query = POS::with("user");

        // Filter by date range
        // Refactor
        if (
            $request->filled("date_from") &&
            $request->filled("date_to") &&
            $request->date_from > $request->date_to
        ) {
            return redirect()
                ->route("history.index")
                ->with(
                    "error",
                    'Tanggal "Dari" tidak boleh lebih besar dari Tanggal "Sampai".',
                );
        }

        if ($request->filled("date_from")) {
            $query->whereDate("created_at", ">=", $request->date_from);
        }

        if ($request->filled("date_to")) {
            $query->whereDate("created_at", "<=", $request->date_to);
        }

        $transactions = $query->latest()->paginate(10)->withQueryString();
        $summaryQuery = POS::where("user_id", $user->id);

        if ($request->filled("date_from") || $request->filled("date_to")) {
            if ($request->filled("date_from")) {
                $summaryQuery->whereDate("created_at", ">=", $request->date_from);
            }
            if ($request->filled("date_to")) {
                $summaryQuery->whereDate("created_at", "<=", $request->date_to);
            }
        } else {
            $summaryQuery->whereDate("created_at", today());
        }

        $summaryTotal = $summaryQuery->sum("total");
        $summaryCount = $summaryQuery->count();

        return view(
            "pages.history",
            compact("title", "subtitle", "transactions", "summaryTotal", "summaryCount"),
        );
    }

    public function show($id)
    {
        $transaction = POS::with([
            "details.product",
            "user",
            'customer'
        ])->find($id);
        if (!$transaction) {
            return response()->json(
                ["success" => false, "message" => "Transaksi tidak ditemukan."],
                404,
            );
        }
        return response()->json([
            "success" => true,
            "transaction" => [
                "id" => $transaction->id,
                "invoice_number" => $transaction->invoice_number,
                "created_at" => $transaction->created_at->format("d M Y, H:i"),
                "user_name" => $transaction->user->name,
                "customer_name" => $transaction->customer ? $transaction->customer->name : null,
                "payment_method" => ucfirst($transaction->payment_method),
                "subtotal" => $transaction->total,
                "total" => $transaction->total,
                "paid_amount" => $transaction->paid_amount,
                "change_amount" => $transaction->change_amount,
                "items" => $transaction->details->map(function ($item) {
                    return [
                        "product_name" => $item->product->name,
                        "quantity" => $item->quantity,
                        "price" => $item->price,
                        "subtotal" => $item->subtotal,
                    ];
                }),
            ],
        ]);
    }
    public function destroy($id)
    {
        $transaction = POS::findOrFail($id);
        $transaction->delete();
        return response()->json([
            "success" => true,
            "message" => "Transaksi berhasil dihapus.",
        ]);
    }
}
