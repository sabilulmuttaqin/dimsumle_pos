<header class="sticky top-0 z-40 flex items-center justify-between h-16 px-4 bg-white border-b border-slate-200">
    <div class="flex items-center space-x-4">
        <button id="headerMenuBtn" type="button" class="lg:hidden text-slate-500 hover:text-slate-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <div>
            <h1 class="text-md font-bold text-slate-900">{{ $title }}</h1>
            <p class="text-sm text-slate-500">{{ $subtitle }}</p>
        </div>
    </div>
    <div class="hidden sm:block">
        <span class="text-sm text-slate-500">{{ now()->format('d M Y') }}</span>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const headerMenuBtn = document.getElementById('headerMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (headerMenuBtn) {
            headerMenuBtn.addEventListener('click', function() {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.style.display = 'flex';
                sidebarOverlay.classList.remove('hidden');
            });
        }
    });
</script>
