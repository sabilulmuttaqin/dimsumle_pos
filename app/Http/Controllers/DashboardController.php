<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\POS;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $title = "Dashboard";
        $subtitle = "Ringkasan performa hari ini";

        $kasirId = $request->input("kasir_id");

        $posQuery = POS::whereDate("created_at", Carbon::today());
        if ($kasirId) {
            $posQuery->where("user_id", $kasirId);
        }

        $kasirList = User::where("role", "kasir")->get();
        $todayTransactions = $posQuery->count();
        $todayRevenue = $posQuery->sum("total");

        $lowStockProducts = Product::where("stock", "<=", 30)->count();

        $recentQuery = POS::with("user")
            ->whereDate("created_at", Carbon::today())
            ->orderBy("created_at", "desc");

        if ($kasirId) {
            $recentQuery->where("user_id", $kasirId);
        }
        $recentTransactions = $recentQuery->get();
        $allUser = User::where('role', 'kasir')->orderBy('name')->get();

        return view(
            "pages.dashboard",
            compact(
                "title",
                "subtitle",
                "kasirList",
                "allUser",
                "todayTransactions",
                "todayRevenue",
                "lowStockProducts",
                "recentTransactions",
            ),
        );
    }

    public function sendTelegramReport(): array
    {
        $transactions = POS::with('details.product')->whereDate('created_at', today())->get();

        if ($transactions->isEmpty()) {
            return ['success' => false, 'message' => 'No transactions today.'];
        }
        $todayRevenue      = $transactions->sum('total');
        $todayTransactions = $transactions->count();
        $paymentMethods = $transactions->groupBy('payment_method')
            ->map(fn($group) => [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ]);
        $allMethods = [
            'cash'     => ['count' => 0, 'total' => 0],
            'transfer' => ['count' => 0, 'total' => 0],
            'qris'     => ['count' => 0, 'total' => 0],
        ];
        foreach ($paymentMethods as $method => $data) {
            $allMethods[strtolower($method)] = $data;
        }
        $allProducts = Product::orderBy('name')->get();
        $date        = now()->format('d M Y');
        $message  = "📊 Laporan Penjualan Harian\n";
        $message .= "📅 Tanggal: {$date}\n\n";
        $message .= "💰 Total Omzet: Rp " . number_format($todayRevenue, 0, ',', '.') . "\n";
        $message .= "🧾 Total Transaksi: {$todayTransactions}\n";
        $cashTotal     = number_format($allMethods['cash']['total'], 0, ',', '.');
        $transferTotal = number_format($allMethods['transfer']['total'], 0, ',', '.');
        $qrisTotal     = number_format($allMethods['qris']['total'], 0, ',', '.');
        $message .= "💵 Cash : {$allMethods['cash']['count']} Transaksi (Rp {$cashTotal})\n";
        $message .= "🏦 TF     : {$allMethods['transfer']['count']} Transaksi (Rp {$transferTotal})\n";
        $message .= "📱 QRIS : {$allMethods['qris']['count']} Transaksi (Rp {$qrisTotal})\n\n";
        $message .= "📝 Rincian Transaksi:\n";
        $no = 1;
        foreach ($transactions as $trx) {
            $items  = $trx->details->map(fn($d) => $d->product->name . ' x' . $d->quantity)->implode(', ');
            $total  = number_format($trx->total, 0, ',', '.');
            $method = strtoupper($trx->payment_method);
            $message .= "{$no}. {$items} - Rp {$total} ({$method})\n";
            $no++;
        }

        $message .= "\n📦 Sisa Stok:\n";
        foreach ($allProducts as $product) {
            $stockEmoji = $product->stock <= 10 ? '⚠️' : '✅';
            $message .= "{$stockEmoji} {$product->name}: {$product->stock}\n";
        }
        $message .= "\nGenerated w/love💖";
        $token  = config('services.telegram.token');
        $chatId = config('services.telegram.chat_id');

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $message,
            'parse_mode' => 'Markdown',
        ]);

        if ($response->successful()) {
            return ['success' => true, 'message' => 'Report sent successfully to Telegram.'];
        }

        return ['success' => false, 'message' => 'Failed to send report. Error: ' . $response->body()];
    }
}
