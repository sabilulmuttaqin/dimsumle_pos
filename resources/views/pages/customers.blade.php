@extends('components.layouts.pages')

@section('content')
    <div class="min-h-screen bg-slate-50" x-data="customerPage()">
        @include('pages.includes.sidebar')

        <!-- Main Content -->
        <div class="lg:pl-64">
            @include('pages.includes.header')

            <main class="p-4">
                <!-- Toast -->
                @include('pages.includes.toast')

                <!-- Main Card -->
                <div class="bg-white rounded-lg border border-slate-200 card-shadow">
                    <!-- Header -->
                    <div class="p-6 border-b border-slate-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Daftar Pelanggan</h2>
                                <p class="text-sm text-slate-500 mt-1">Kelola data pelanggan untuk transaksi</p>
                            </div>
                            <button @click="openCreateModal()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium w-full md:w-auto">
                                + Tambah Pelanggan
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Nama Pelanggan</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($customers as $index => $customer)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-slate-900">
                                            {{ $customers->firstItem() + $index }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-900">
                                            {{ $customer->name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button @click="openEditModal({{ $customer->id }})"
                                                    class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button @click="openDeleteModal({{ $customer->id }})"
                                                    class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-slate-300 mb-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                    </path>
                                                </svg>
                                                <p class="text-slate-500">Belum ada data pelanggan</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($customers->hasPages())
                        <div class="px-6 py-4 border-t border-slate-200">
                            {{ $customers->links() }}
                        </div>
                    @endif
                </div>
            </main>
        </div>

        <!-- Add/Edit Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/50 transition-opacity" @click="closeModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="save()">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-semibold text-slate-900 mb-4"
                                x-text="isEdit ? 'Edit Pelanggan' : 'Tambah Pelanggan'">Tambah Pelanggan</h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="cust_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Pelanggan</label>
                                    <input id="cust_name" type="text" x-model="form.name"
                                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                        placeholder="Masukkan nama pelanggan">
                                    <template x-if="errors.name">
                                        <span class="text-red-500 text-xs" x-text="errors.name[0]"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="loading"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                                <span x-show="!loading" x-text="isEdit ? 'Perbarui' : 'Simpan'"></span>
                                <span x-show="loading">
                                    ...
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

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script src="{{ asset('js/customer.js') }}"></script>
@endsection
