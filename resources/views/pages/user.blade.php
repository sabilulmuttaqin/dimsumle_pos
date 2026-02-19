@extends('components.layouts.pages')

@section('content')
    <div class="min-h-screen bg-slate-50" x-data="userPage()">
        @include('pages.includes.sidebar')

        <div class="lg:pl-64">
            @include('pages.includes.header')

            <main class="p-4">
                <div x-show="toast.show" x-cloak x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-2" class="fixed top-4 right-4 z-[60] max-w-sm w-full">
                    <div class="bg-white rounded-lg shadow-lg border border-green-200 p-4 flex items-start space-x-3">
                        <div class="shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900" x-text="toast.message"></p>
                        </div>
                        <button @click="toast.show = false" class="shrink-0 text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="bg-white rounded-lg border border-slate-200 card-shadow">
                    <!-- Header -->
                    <div class="p-6 border-b border-slate-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Daftar User</h2>
                                <p class="text-sm text-slate-500 mt-1">Kelola akun kasir</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button @click="openCreateModal()"
                                    class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah User
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                        Role</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse($users as $user)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 bg-slate-100 rounded-full mr-3">
                                                    <span
                                                        class="text-sm font-semibold text-slate-700">{{ substr($user->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-slate-900">{{ $user->name }}
                                                    </div>
                                                    @if ($user->id === auth()->id())
                                                        <span class="text-xs text-slate-500">(Anda)</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-slate-900">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $user->role === 'owner' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                            <button @click="openEditModal({{ $user->id }})"
                                                class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-xs font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                                                Edit
                                            </button>
                                            @if ($user->id !== auth()->id())
                                                <button
                                                    @click="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                    class="inline-flex items-center px-3 py-1.5 border border-red-300 text-red-700 text-xs font-medium rounded-lg bg-white hover:bg-red-50 hover:border-red-400 transition-colors">
                                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                </path>
                                            </svg>
                                            <p class="text-slate-500 font-medium">Tidak ada user ditemukan</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-slate-200">
                        {{ $users->links() }}
                    </div>
                </div>
            </main>
        </div>

        <!-- Modal Form -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/50" @click="closeModal()"></div>

                <div
                    class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="save()">
                        <!-- Header -->
                        <div class="bg-white px-6 py-4 border-b border-slate-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-slate-900"
                                    x-text="isEdit ? 'Edit User' : 'Tambah User Baru'"></h3>
                                <button type="button" @click="closeModal()"
                                    class="text-slate-400 hover:text-slate-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="bg-white px-6 py-4 space-y-4">
                            <!-- Nama -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="form.name"
                                    class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                <template x-if="errors.name">
                                    <p class="mt-1 text-xs text-red-600" x-text="errors.name[0]"></p>
                                </template>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" x-model="form.email"
                                    class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                <template x-if="errors.email">
                                    <p class="mt-1 text-xs text-red-600" x-text="errors.email[0]"></p>
                                </template>
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select x-model="form.role"
                                    class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent bg-white">
                                    <option value="kasir">Kasir</option>
                                    <option value="admin">Owner</option>
                                </select>
                                <template x-if="errors.role">
                                    <p class="mt-1 text-xs text-red-600" x-text="errors.role[0]"></p>
                                </template>
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Password <span x-show="!isEdit" class="text-red-500">*</span>
                                    <template x-if="isEdit">
                                        <span class="text-xs text-slate-500">(Kosongkan jika tidak ingin mengubah)</span>
                                    </template>
                                </label>
                                <input type="password" x-model="form.password"
                                    class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                <template x-if="errors.password">
                                    <p class="mt-1 text-xs text-red-600" x-text="errors.password[0]"></p>
                                </template>
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-200">
                            <button type="button" @click="closeModal()"
                                class="px-4 py-2.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 transition-colors text-sm font-medium">
                                Batal
                            </button>
                            <button type="submit" :disabled="loading"
                                class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium disabled:opacity-50">
                                <span x-show="!loading" x-text="isEdit ? 'Update' : 'Simpan'"></span>
                                <span x-show="loading">
                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/50" @click="showDeleteModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="relative z-10 inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 py-4">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-slate-900 mb-2" x-text="deleteTitle"></h3>
                            <p class="text-sm text-slate-500" x-text="deleteDescription"></p>
                        </div>
                        <div class="mt-6 flex justify-center space-x-3">
                            <button type="button" @click="showDeleteModal = false"
                                class="px-5 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors font-medium text-sm">
                                Batal
                            </button>
                            <button type="button" @click="destroy()" :disabled="loading"
                                class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm flex items-center space-x-2 disabled:opacity-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                <span>Ya, Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script src="{{ asset('js/user.js') }}"></script>
@endsection
