@extends('components.layouts.pages')

@section('content')
<div class="min-h-screen bg-slate-50" x-data="expensePage()">
    @include('pages.includes.sidebar')

    <!-- Main Content -->
    <div class="lg:pl-64">
        @include('pages.includes.header')

        <main class="p-4">
            @include('pages.includes.toast')
            @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg flex items-center space-x-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            @endif
            <div class="bg-white rounded-lg border border-slate-200 card-shadow">
                <!-- Header -->
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col space-y-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Riwayat Pengeluaran</h2>
                                <p class="text-sm text-slate-500 mt-1">Daftar semua pengeluaran yang telah dicatat</p>
                            </div>
                            <button @click="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium w-full md:w-auto">
                                + Tambah Pengeluaran
                            </button>
                        </div>

                        <!-- Filters (form GET — page reload) -->
                        <form action="{{ route('expenses.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-3">
                            <div class="flex space-x-2">
                                <div class="flex flex-col w-full">
                                    <label for="exp_date_from" class="text-xs font-medium text-slate-500 mb-1">Dari</label>
                                    <input id="exp_date_from" type="date" name="date_from" value="{{ request('date_from') }}"
                                        class="px-4 py-2.5 text-sm border border-slate-300 rounded-lg w-full md:w-auto focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                </div>
                                <div class="flex flex-col w-full">
                                    <label for="exp_date_to" class="text-xs font-medium text-slate-500 mb-1">Sampai</label>
                                    <input id="exp_date_to" type="date" name="date_to" value="{{ request('date_to') }}"
                                        class="px-4 py-2.5 text-sm border border-slate-300 rounded-lg w-full md:w-auto focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                </div>
                            </div>

                            <div class="flex space-x-2 flex-col">
                            <button type="submit" class="px-4 py-2.5 bg-slate-700 text-white text-sm rounded-lg hover:bg-slate-600 transition-colors font-medium">
                                Filter
                            </button>
                            </div>
                            
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Tanggal</th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($expenses as $expense)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-slate-900">
                                        {{ $expense->expense_date->format('d/m/Y') }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ $expense->description ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                        Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <button @click="openEditModal({{ $expense->id }})" class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-xs font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button @click="openDeleteModal({{ $expense->id }})" class="inline-flex items-center px-3 py-1.5 border border-red-300 text-red-700 text-xs font-medium rounded-lg bg-white hover:bg-red-50 hover:border-red-400 transition-colors">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-slate-500">Belum ada data pengeluaran</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($expenses->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200">
                        {{ $expenses->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Add/Edit Expense Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 transition-opacity" @click="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="save()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4" x-text="isEdit ? 'Edit Pengeluaran' : 'Tambah Pengeluaran'">Tambah Pengeluaran</h3>

                        <div class="space-y-4">


                            <!-- Jumlah -->
                            <div>
                                <label for="exp-amount" class="block text-sm font-medium text-slate-700 mb-1">Jumlah (Rp)</label>
                                <input id="exp-amount" type="number" x-model="form.amount" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent" placeholder="0">
                                <template x-if="errors.amount">
                                    <span class="text-red-500 text-xs" x-text="errors.amount[0]"></span>
                                </template>
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label for="exp-date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                                <input id="exp-date" type="date" x-model="form.expense_date" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                <template x-if="errors.expense_date">
                                    <span class="text-red-500 text-xs" x-text="errors.expense_date[0]"></span>
                                </template>
                            </div>

                            <!-- Keterangan -->
                            <div>
                                <label for="exp-description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                                <textarea id="exp-description" x-model="form.description" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent" placeholder="Masukkan Deskripsi..."></textarea>
                                <template x-if="errors.description">
                                    <span class="text-red-500 text-xs" x-text="errors.description[0]"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" :disabled="loading"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-show="!loading" x-text="isEdit ? 'Perbarui' : 'Simpan'"></span>
                            <span x-show="loading">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                        <button type="button" @click="closeModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        
    @include('pages.includes.deletemodal')

</div>

<style>[x-cloak] { display: none !important; }</style>

<script src="{{ asset('js/expenses.js') }}"></script>

@endsection
