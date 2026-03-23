@extends('components.layouts.pages')

@section('content')
<div class="min-h-screen bg-slate-50">
    <!-- Sidebar -->
    @include('pages.includes.sidebar')

    <!-- Main Content -->
    <div class="lg:pl-64 transition-all duration-300">
        <!-- Top Bar -->
        @include('pages.includes.header')

        <!-- Page Content -->
        <main class="p-6">

            @if (auth()->user()->isOwner() && $lowStockProducts > 0)
                <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-amber-900">Stok Rendah</h4>
                            <p class="text-sm text-amber-700">{{ $lowStockProducts }} produk stok kurang dari 30</p>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
                @if (auth()->user()->isOwner())
                <form action="{{ route('dashboard') }}" method="GET" class="stats-card bg-white rounded-lg p-5 card-shadow flex flex-col justify-center h-full">
                    <label for="kasir_id" class="block text-sm font-medium text-slate-600 mb-2">Filter Kasir</label>
                    <select name="kasir_id" id="kasir_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent bg-slate-50 text-slate-900">
                        <option value="">Semua Kasir</option>
                        @foreach ($allUser as $kasir)
                            <option value="{{ $kasir->id }}" {{ request('kasir_id') == $kasir->id ? 'selected' : '' }}>{{ $kasir->name }}</option>
                        @endforeach
                    </select>
                </form>
                @endif
                <div class="stats-card bg-white rounded-lg p-5 card-shadow">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-50 rounded-lg border border-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900 mb-1">{{ $todayTransactions }}</h3>
                            <p class="text-sm text-slate-600">Transaksi Hari Ini</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="stats-card bg-white rounded-lg p-5 card-shadow">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-50 rounded-lg border border-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900 mb-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                            <p class="text-sm text-slate-600">Pendapatan Hari Ini</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
                <!-- Recent Transactions -->
                <div id="transaction-list" class="bg-white rounded-lg border border-slate-200 card-shadow">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h2 class="font-semibold text-slate-900">Transaksi Hari Ini</h2>
                    </div>
                    <div class="p-5">
                        @if ($recentTransactions->count() > 0)
                            <div class="space-y-4">
                                @foreach ($recentTransactions as $transaction)
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                                        <div class="flex-1">
                                            <p class="font-medium text-slate-900 text-sm">
                                                {{ $transaction->invoice_number }}</p>
                                            <p class="text-xs text-slate-500 mt-1">{{ $transaction->user->name }} •
                                                {{ $transaction->created_at->format('H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-slate-900">Rp
                                                {{ number_format($transaction->total, 0, ',', '.') }}</p>
                                            <span class="inline-block text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded mt-1">
                                                {{ $transaction->payment_method }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                <p class="text-slate-500 text-sm">Belum ada transaksi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
