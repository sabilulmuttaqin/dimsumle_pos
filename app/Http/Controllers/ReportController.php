<?php

namespace App\Http\Controllers;

use App\Models\POS;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\DateHelper;

class ReportController extends Controller
{
    public function index()
    {
        return view('pages.report', [
            'title'    => 'Laporan',
            'subtitle' => 'Analisis dan ringkasan penjualan',
            'cashiers' => User::where('role', 'kasir')->orderBy('name')->get(),
        ]);
    }

    public function getData(Request $request)
    {
        $month   = $request->input('month', now()->month);
        $year    = $request->input('year', now()->year);
        $cashier = $request->input('cashier', '');
        $start   = Carbon::create($year, $month, 1)->startOfMonth();
        $end     = Carbon::create($year, $month, 1)->endOfMonth();

        $metrics  = $this->loadMetrics($start, $end, $cashier);
        $chart    = $this->loadChartData($start, $end, $cashier);
        $expenses = $this->loadExpenseChartData($start, $end, $cashier);
        $products = $this->loadTopProducts($start, $end, $cashier);
        $profit   = $metrics['totalSales'] - $expenses['totalExpenses'];

        return response()->json([
            'totalSales'       => $metrics['totalSales'],
            'totalTransactions' => $metrics['totalTransactions'],
            'totalProducts'    => $metrics['totalProducts'],
            'profit'           => $profit,
            'totalExpenses'    => $expenses['totalExpenses'],
            'chartLabels'      => $chart['labels'],
            'chartData'        => $chart['data'],
            'chartDates'       => $chart['dates'],
            'expenseChartData' => $expenses['data'],
            'topProducts'      => $products->map(fn($product) => [
                'name'              => $product->name,
                'total_sales'       => $product->total_sales,
                'transaction_count' => $product->transaction_count,
            ])->values(),

            'topCustomers' => $this->loadTopCustomers($start, $end, $cashier)
                ->map(fn($customer) => [
                    'name'              => $customer->customer_name,
                    'transaction_count' => $customer->transaction_count,
                    'total_spend'       => $customer->total_spend,
                ])->values(),
        ]);
    }

    private function withCashier($query, $cashier)
    {
        return $cashier ? $query->where('user_id', $cashier) : $query;
    }

    private function loadMetrics($start, $end, $cashier)
    {
        $transactions = $this->withCashier(POS::whereBetween('updated_at', [$start, $end]), $cashier)->get();

        $productsQuery = DB::table('transaction_details')
            ->join('pos', 'transaction_details.pos_id', '=', 'pos.id')
            ->whereBetween('pos.updated_at', [$start, $end]);
        if ($cashier) {
            $productsQuery->where('pos.user_id', $cashier);
        }

        return [
            'totalSales'        => $transactions->sum('total'),
            'totalTransactions' => $transactions->count(),
            'totalProducts'     => $productsQuery->sum('transaction_details.quantity'),
        ];
    }

    private function loadChartData($start, $end, $cashier)
    {
        $dates = collect();
        for ($current = $start->copy(); $current->lte($end); $current->addDay()) {
            $dates->push($current->format('Y-m-d'));
        }

        $salesPerDate = $this->withCashier(POS::whereBetween('updated_at', [$start, $end]), $cashier)
            ->selectRaw('DATE(updated_at) as date, SUM(total) as total_sales')
            ->groupBy('date')->orderBy('date')
            ->pluck('total_sales', 'date')->toArray();

        $labels = $dates->map(function ($date) {
            $carbon    = Carbon::parse($date);
            $dayNumber = $carbon->day;
            return ($dayNumber === 1 || ($dayNumber - 1) % 3 === 0 || $dayNumber === $carbon->daysInMonth)
                ? $dayNumber : '';
        });

        return [
            'labels' => $labels->values()->all(),
            'data'   => $dates->map(fn($date) => (float) ($salesPerDate[$date] ?? 0))->values()->all(),
            'dates'  => $dates->map(fn($date) => [
                'date' => Carbon::parse($date)->format('d/m/Y'),
                'day'  => DateHelper::getDayName(Carbon::parse($date)->dayOfWeek),
            ])->values()->all(),
        ];
    }

    private function loadExpenseChartData($start, $end, $cashier)
    {
        $dates = collect();
        for ($current = $start->copy(); $current->lte($end); $current->addDay()) {
            $dates->push($current->format('Y-m-d'));
        }

        $expensesPerDate = $this->withCashier(Expense::whereBetween('expense_date', [$start, $end]), $cashier)
            ->selectRaw('DATE(expense_date) as date, SUM(amount) as total_expense')
            ->groupBy('date')->orderBy('date')
            ->pluck('total_expense', 'date')->toArray();

        $data = $dates->map(fn($date) => (float) ($expensesPerDate[$date] ?? 0));

        return ['data' => $data->values()->all(), 'totalExpenses' => $data->sum()];
    }

    private function loadTopProducts($start, $end, $cashier)
    {
        $query = DB::table('transaction_details')
            ->join('pos', 'transaction_details.pos_id', '=', 'pos.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('pos.updated_at', [$start, $end]);
        if ($cashier) {
            $query->where('pos.user_id', $cashier);
        }

        return $query->select(
            'products.id',
            'products.name',
            DB::raw('SUM(transaction_details.quantity) as total_quantity'),
            DB::raw('SUM(transaction_details.subtotal) as total_sales'),
            DB::raw('COUNT(DISTINCT pos.id) as transaction_count')
        )->groupBy('products.id', 'products.name')->orderByDesc('total_sales')->get();
    }
    private function loadTopCustomers($start, $end, $cashier)
    {
        $query = DB::table('pos')
            ->join('customers', 'pos.customer_id', '=', 'customers.id')
            ->whereBetween('pos.updated_at', [$start, $end])
            ->whereNotNull('pos.customer_id');
        if ($cashier) {
            $query->where('pos.user_id', $cashier);
        }

        return $query->select(
            'customers.name as customer_name',
            DB::raw('COUNT(pos.id) as transaction_count'),
            DB::raw('SUM(pos.total) as total_spend')
        )->groupBy('customers.id', 'customers.name')->orderByDesc('transaction_count')->limit(5)->get();
    }
}
