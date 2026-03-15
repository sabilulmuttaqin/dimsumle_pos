@extends('components.layouts.pages')

@section('content')
<div class="min-h-screen bg-slate-50" x-data="reportPage()">
    @include('pages.includes.sidebar')

    <!-- Main Content -->
    <div class="lg:pl-64">
        @include('pages.includes.header')

        <!-- Dashboard Content -->
        <main class="p-6">
            <!-- Filter Row -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-3 md:space-y-0">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Month Dropdown -->
                    <select x-model="selectedMonth" id="selectedMonth" @change="loadData()"
                        class="px-4 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent bg-white">
                        @php $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                        @foreach ($months as $i => $month)
                            <option value="{{ $i + 1 }}" {{ date('n') == $i + 1 ? 'selected' : '' }}>{{ $month }}</option>
                        @endforeach
                    </select>

                    <!-- Year Dropdown -->
                    <select x-model="selectedYear" id="selectedYear" @change="loadData()"
                        class="px-4 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent bg-white">
                        @for ($year = date('Y') - 3; $year <= date('Y') + 1; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Cashier Dropdown -->
                    <select x-model="selectedCashier" @change="loadData()"
                        class="px-4 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent bg-white">
                        <option value="">Keseluruhan</option>
                        @foreach ($cashiers as $cashier)
                            <option value="{{ $cashier->id }}">{{ $cashier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <p class="text-sm text-slate-500 mt-2 mb-4">
                Menampilkan data: 
                <span class="font-medium text-slate-700" 
                x-text="months[selectedMonth - 1] + ' ' + selectedYear + ' - ' + (selectedCashier === '' ? 'Keseluruhan' : selectedCashierName)">
                </span>
            </p>
            <!-- Loading Overlay -->
            <div x-show="loading" class="flex items-center justify-center py-12">
                <svg class="animate-spin h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <div x-show="!loading" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4 mb-4">
                    <div class="stats-card bg-white rounded-lg p-5 card-shadow border border-slate-200">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-white rounded-lg">
                                <svg class="w-6 h-6 text-black-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-900" x-text="formatRupiah(totalSales)"></p>
                                <p class="text-sm text-slate-600 mb-1">Total Penjualan</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Pengeluaran -->
                    <div class="stats-card bg-white rounded-lg p-5 card-shadow border border-slate-200">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-white rounded-lg">
                                <svg class="w-6 h-6 text-black-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-900" x-text="formatRupiah(totalExpenses)"></p>
                                <p class="text-sm text-slate-600 mb-1">Total Pengeluaran</p>
                            </div>
                        </div>
                    </div>

                    <!-- Laba (Profit) -->
                    <div class="stats-card bg-white rounded-lg p-5 card-shadow border border-slate-200">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-black-50 rounded-lg">
                                <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-900" x-text="formatRupiah(profit)"></p>
                                <p class="text-sm text-slate-600 mb-1">Laba</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Chart and Insights -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                    <!-- Sales Chart -->
                    <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 card-shadow">
                        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                            <div>
                                <h2 class="font-semibold text-slate-900">Grafik Penjualan (Omset)</h2>
                                <p class="text-xs text-slate-500 mt-1">
                                    Periode: <span x-text="months[selectedMonth - 1]"></span> <span x-text="selectedYear"></span>
                                </p>
                            </div>
                            <div class="flex items-center gap-6 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                    <span class="text-slate-600">Omzet</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-slate-500"></span>
                                    <span class="text-slate-600">Pengeluaran</span>
                                </div>
                            </div>
                        </div>
                       
                        <div class="p-5">
                            <div class="overflow-x-auto">
                                <div class="h-80 min-w-[600px]">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Teks -->
                    <div class="bg-white rounded-lg border border-slate-200 card-shadow flex flex-col h-full ">
                        <div class="px-5 py-4 border-b border-slate-200">
                            <h2 class="font-semibold text-slate-900">Ringkasan Teks</h2>
                            <p class="text-xs text-slate-500 mt-1">Ringkasan periode ini</p>
                        </div>
                        <div class="p-5 flex-1 lg:relative rounded-b-lg">
                            
                        </div>
                    </div>
                </div>

                <!-- Tables -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Top Products -->
                    <div class="bg-white rounded-lg border border-slate-200 card-shadow">
                        <div class="px-5 py-4 border-b border-slate-200">
                            <h2 class="font-semibold text-slate-900">Produk Terlaris</h2>
                            <p class="text-xs text-slate-500 mt-1">Produk yang sering terjual</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 tracking-wider">Nama</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 tracking-wider">Omzet</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 tracking-wider">Jml</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    <template x-if="topProducts.length > 0">
                                        <template x-for="(product, idx) in topProducts.slice(0, 5)" :key="idx">
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-4 py-3 text-sm font-medium text-slate-900 truncate max-w-[120px]" x-text="product.name"></td>
                                                <td class="px-4 py-3 text-sm text-slate-900 font-medium" x-text="formatRupiah(product.total_sales)"></td>
                                                <td class="px-4 py-3 text-sm text-slate-600" x-text="product.transaction_count"></td>
                                            </tr>
                                        </template>
                                    </template>
                                    <template x-if="topProducts.length === 0">
                                        <tr>
                                            <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-500">Belum ada data</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Top Customers -->
                    <div class="bg-white rounded-lg border border-slate-200 card-shadow">
                        <div class="px-5 py-4 border-b border-slate-200">
                            <h2 class="font-semibold text-slate-900">5 Pembeli Terbanyak</h2>
                            <p class="text-xs text-slate-500 mt-1">Pelanggan dengan transaksi tertinggi</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 tracking-wider">Nama</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 tracking-wider">Frekuensi Beli</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 tracking-wider">Total Rp</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    <tr>
                                        <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-500">Belum ada pelanggan</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/report.js') }}"></script>

@endsection
