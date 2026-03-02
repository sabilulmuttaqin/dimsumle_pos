@extends('components.layouts.pages')

@section('content')
    @include('pages.includes.sidebar')

    <div class="lg:pl-64">
        @include('pages.includes.header')
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
    </div>
@endsection
