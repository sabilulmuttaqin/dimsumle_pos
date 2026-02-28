<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\POS;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        $title = 'Transaksi';
        $subtitle = 'Catat transaksi lebih mudah';
        $products = Product::where('stock', '>', 0)->get();

        return view('pages.pos', compact('title', 'subtitle', 'products'));
    }

    public function store(Request $request)
    {
        $numericRule = 'required|numeric|min:0';
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => $numericRule,
            'cart.*.subtotal' => $numericRule,
            'paid_amount' => $numericRule,
            'payment_method' => 'required|in:cash,qris,transfer',
        ]);

        $total = 0;
        foreach ($request->cart as $item) {
            $total += $item['subtotal'];
        }
        $paidAmount = (float) $request->paid_amount;
        $changeAmount = max(0, $paidAmount - $total);

        if ($paidAmount < $total) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah pembayaran kurang dari total transaksi.',
            ], 422);
        }

        try {
            $transaction = DB::transaction(function () use ($request, $total, $paidAmount, $changeAmount) {
                $transaction = POS::create([
                    'user_id' => auth()->id(),
                    'total' => $total,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => $request->payment_method,
                ]);
                foreach ($request->cart as $item) {
                    Product::find($item['id'])->decrement('stock', $item['quantity']);
                }
                return $transaction;
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses!',
                'invoice' => $transaction->invoice_number,
                'id' => $transaction->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
