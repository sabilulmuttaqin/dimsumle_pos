@extends('components.layouts.pages')

@section('content')
<div class="min-h-screen bg-slate-50" x-data="historyPage()">
    @include('pages.includes.sidebar')

    <!-- Main Content -->
    <div class="lg:pl-64">
        @include('pages.includes.header')

        <main class="p-4">
            <!-- Toast Notification -->
            @include('pages.includes.toast')

            <div class="bg-white rounded-lg border border-slate-200 card-shadow">
                <!-- Header -->
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col space-y-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Riwayat Transaksi</h2>
                                <p class="text-sm text-slate-500 mt-1">Daftar semua transaksi yang telah dilakukan</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Pendapatan Hari Ini -->
                            <div class="stats-card bg-white rounded-lg p-5 card-shadow border border-slate-200">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center justify-center w-12 h-12 bg-white rounded-lg">
                                        <svg class="w-6 h-6 text-black-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-slate-900">Rp {{ number_format($summaryTotal, 0, ',', '.') }}</p>
                                        <p class="text-sm text-slate-600 mb-1">Pendapatan</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Jumlah Transaksi Hari Ini -->
                            <div class="stats-card bg-white rounded-lg p-5 card-shadow border border-slate-200">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center justify-center w-12 h-12 bg-white rounded-lg">
                                        <svg class="w-6 h-6 text-black-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-slate-900">{{ $summaryCount }}</p>
                                        <p class="text-sm text-slate-600 mb-1">Transaksi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Filter Form -->
                        <form action="{{ route('history.index') }}" method="GET" class="flex flex-col md:flex-row md:items-start space-y-3 md:space-y-0 md:space-x-3">
                            <div class="flex space-x-2">
                                <div class="flex flex-col w-full">
                                    <label class="text-xs font-medium text-slate-500 mb-1">Dari</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                                        class="px-4 py-2.5 text-sm border border-slate-300 rounded-lg w-full md:w-auto focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                </div>
                                <div class="flex flex-col w-full">
                                    <label class="text-xs font-medium text-slate-500 mb-1">Sampai</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                                        class="px-4 py-2.5 text-sm border border-slate-300 rounded-lg w-full md:w-auto focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                </div>
                            </div>
                            <div class="flex space-x-2 self-end pt-5">
                                <button type="submit" class="px-4 py-2.5 bg-slate-700 text-white text-sm rounded-lg hover:bg-slate-600 transition-colors font-medium">
                                    Filter
                                </button>
                                @if(request('date_from') || request('date_to'))
                                    <a href="{{ route('history.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 text-sm rounded-lg hover:bg-slate-200 transition-colors font-medium border border-slate-200 text-center flex items-center justify-center">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    @if(session('error'))
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                    @endif
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Metode</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-slate-900">{{ $transaction->invoice_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">{{ $transaction->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ $transaction->created_at->format('H:i') }}</div>
</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-slate-900">Rp {{ number_format($transaction->total, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ ucfirst($transaction->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                        <button @click="viewDetail({{ $transaction->id }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-xs font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                                            Detail
                                        </button>
                                        <button @click="openDeleteModal({{ $transaction->id }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-red-300 text-red-700 text-xs font-medium rounded-lg bg-white hover:bg-red-50 transition-colors">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                        <p class="text-slate-500 font-medium">Tidak ada transaksi ditemukan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $transactions->links() }}
                </div>
            </div>
        </main>
    </div>

    <!-- Detail Modal -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/50" @click="closeDetailModal()"></div>

            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <!-- Header -->
                <div class="bg-white px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Detail Transaksi</h3>
                        <button type="button" @click="closeDetailModal()"
                            class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <template x-if="selectedTransaction">
                    <div class="bg-white px-6 py-4">
                        <!-- Transaction Info -->
                        <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-slate-50 rounded-lg border border-slate-200">
                            <div>
                                <p class="text-sm text-slate-500">Invoice</p>
                                <p class="font-semibold text-slate-900" x-text="selectedTransaction.invoice_number"></p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Tanggal</p>
                                <p class="font-semibold text-slate-900" x-text="selectedTransaction.created_at"></p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Kasir</p>
                                <p class="font-semibold text-slate-900" x-text="selectedTransaction.user_name"></p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Metode Pembayaran</p>
                                <p class="font-semibold text-slate-900" x-text="selectedTransaction.payment_method"></p>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-slate-900 mb-3">Item Produk</h4>
                            <div class="space-y-3">
                                <template x-for="item in selectedTransaction.items" :key="item.product_name">
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                                        <div class="flex-1">
                                            <p class="font-medium text-slate-900" x-text="item.product_name"></p>
                                            <p class="text-sm text-slate-500" x-text="item.quantity + ' x Rp ' + formatRupiah(item.price)"></p>
                                        </div>
                                        <p class="font-semibold text-slate-900" x-text="'Rp ' + formatRupiah(item.subtotal)"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="border-t border-slate-200 pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Subtotal</span>
                                <span class="font-medium text-slate-900" x-text="'Rp ' + formatRupiah(selectedTransaction.subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-lg font-semibold pt-2 border-t border-slate-200">
                                <span class="text-slate-900">Total</span>
                                <span class="text-slate-900" x-text="'Rp ' + formatRupiah(selectedTransaction.total)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Dibayar</span>
                                <span class="font-medium text-slate-900" x-text="'Rp ' + formatRupiah(selectedTransaction.paid_amount)"></span>
                            </div>
                            <template x-if="selectedTransaction.change_amount > 0">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Kembalian</span>
                                    <span class="font-medium text-green-600" x-text="'Rp ' + formatRupiah(selectedTransaction.change_amount)"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Footer -->
                <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-200">
                <button type="button" @click="
                    let frame = document.getElementById('struk-frame');
                    frame.src = '/struk/' + selectedTransaction.id;
                    frame.onload = () => frame.contentWindow.print();
                    "
                        class="px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">
                        Cetak
                    </button>    
                <button type="button" @click="closeDetailModal()"
                        class="px-4 py-2.5 border border-slate-300 text-slate-900 rounded-lg transition-colors text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
<iframe id="struk-frame" style="display:none;"></iframe>
    @include('pages.includes.deletemodal')
</div>

<style>[x-cloak] { display: none !important; }</style>

<script src="{{ asset('js/history.js') }}"></script>
@endsection