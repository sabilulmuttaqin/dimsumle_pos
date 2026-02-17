@extends('components.layouts.pages')

@section('content')
    <div class="min-h-screen w-full flex justify-center items-center" x-data="loginForm()">
        <!-- Background Gambar Pattern Dimsum dengan Opacity 50% -->
        <div class="fixed inset-0 z-0 opacity-10 pointer-events-none"
            style="background-image: url('{{ asset('images/bg-login.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        </div>

        <div class="w-full max-w-md p-8 relative z-10">
            <div>
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-24 h-24 mx-auto mb-2">
            </div>
            <div class="text-center mb-2">
                <h1 class="text-2xl font-bold text-slate-900 mb-2">Sign In</h1>
                <p class="text-slate-500 text-sm">Silakan login untuk melanjutkan ke dashboard POS Dimsum Lebih Enak</p>
            </div>

            <form @submit.prevent="submit" class="space-y-5" novalidate>
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                    <input type="email" id="email" x-model="form.email" placeholder="nama@email.com"
                        class="block bg-white w-full px-3 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-0 focus:border-slate-300">
                    <template x-if="errors.email">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.email[0]"></p>
                    </template>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" id="password" x-model="form.password"
                            placeholder="********"
                            class="block w-full px-3 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent pr-10">

                        <!-- Tombol Mata (Toggle) -->
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <template x-if="errors.password">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.password[0]"></p>
                    </template>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" id="remember" x-model="form.remember"
                        class="h-4 w-4 text-slate-900 focus:ring-blue-600 border-slate-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-slate-700">Ingat saya</label>
                </div>

                <!-- Submit -->
                <button type="submit" :disabled="loading"
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-all duration-200 font-medium disabled:opacity-50">
                    <span x-show="!loading">Masuk</span>
                    <span x-show="loading">
                        <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                    </span>
                </button>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>
@endsection
