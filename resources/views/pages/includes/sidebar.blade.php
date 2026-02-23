<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-slate-200">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10">
            <span class="text-md font-bold text-slate-900">Dimsum L.E</span>
        </div>
        <!-- Close Button - Visible on Desktop -->
        <button id="sidebarToggle" type="button"
            class="text-slate-400 hover:text-slate-700 focus:outline-none p-1.5 rounded-lg hover:bg-slate-100 lg:hidden transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
    </div>

    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <div class="flex justify-between items-center">
            <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
            <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</p>
        </div>
    </div>

    <nav class="p-3 space-y-3 overflow-y-auto max-h-[calc(100vh-180px)]">

        <div>
            <h3 class="px-3 text-[11px] font-semibold text-slate-500 uppercase mb-2">Home</h3>
            <div class="space-y-1">
                @if (auth()->user()->isOwner())
                    <a href="{{ route('dashboard') }}"
                        class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'text-white bg-blue-600 hover:bg-blue-600 hover:text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        <span class="font-medium text-sm">Dashboard</span>
                    </a>
                @endif
            </div>
        </div>
        <div>

            <div class="space-y-1">
                @if (auth()->user()->isKasir())
                    <a href="{{ route('pos.index') }}"
                        class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('pos.*') ? 'text-white bg-blue-600 hover:bg-blue-600 hover:text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span class="font-medium text-sm">Transaksi</span>
                    </a>
                @endif
            </div>
        </div>
        @if (auth()->user()->isOwner())
            <div>
                <h3 class="px-3 text-[11px] font-semibold text-slate-500 uppercase mb-2">Manajemen</h3>
                <div class="space-y-1">
                    <a href="{{ route('users.index') }}"
                        class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('users.*') ? 'text-white bg-blue-600 hover:bg-blue-600 hover:text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span class="font-medium text-sm">Akun Kasir</span>
                    </a>
                    <a href="{{ route('products.index') }}"
                    class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('products.*') ? 'text-white bg-blue-600 hover:bg-blue-600 hover:text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z">
                        </path>
                    </svg>
                    <span class="font-medium text-sm">Produk</span>
                    </a>
                    <a href="{{ route('expenses.index') }}"
                        class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('expenses.*') ? 'text-white bg-blue-600 hover:bg-blue-600 hover:text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span class="font-medium text-sm">Pengeluaran</span>
                    </a>
                </div>
            </div>
        @endif
        <div class="absolute bottom-0 left-0 right-0 p-3 bg-white border-t border-slate-200">

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="sidebar-link flex w-full items-center space-x-3 px-3 py-2.5 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors">
                    <span class="font-medium text-sm">Logout</span>
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                </button>
            </form>
        </div>
    </nav>

</aside>

<!-- Overlay untuk mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" style="display: none;"></div>

<!-- Toggle button untuk main content (muncul saat sidebar minimize di desktop) -->
<button id="sidebarOpenBtn" type="button"
    class="fixed top-4 left-4 z-50 hidden items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-lg shadow-sm text-slate-500 hover:text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOpenBtn = document.getElementById('sidebarOpenBtn');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        let isSidebarMinimized = false;

        function isDesktop() {
            return window.innerWidth >= 1024;
        }

        // Toggle sidebar saat tombol close diklik
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (isDesktop()) {
                // Desktop: minimize sidebar
                isSidebarMinimized = true;
                if (isSidebarMinimized) {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOpenBtn.classList.remove('hidden');
                    sidebarOpenBtn.classList.add('flex');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    sidebarOpenBtn.classList.add('hidden');
                    sidebarToggle.classList.add('hidden');
                    sidebarOpenBtn.classList.remove('flex');
                }
            } else {
                // Mobile: toggle dengan overlay
                const isHidden = sidebar.classList.contains('-translate-x-full');
                if (isHidden) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebarOverlay.style.display = 'flex';
                    sidebarOverlay.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.style.display = 'none';
                    sidebarOverlay.classList.add('hidden');
                }
            }
        });

        // Buka sidebar saat tombol open diklik (desktop)
        sidebarOpenBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            sidebar.classList.remove('-translate-x-full');
            sidebarOpenBtn.classList.add('hidden');
            sidebarOpenBtn.classList.remove('flex');
            isSidebarMinimized = false;
        });

        // Tutup sidebar saat overlay diklik (mobile)
        sidebarOverlay.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.style.display = 'none';
            sidebarOverlay.classList.add('hidden');
        });
    });
</script>
