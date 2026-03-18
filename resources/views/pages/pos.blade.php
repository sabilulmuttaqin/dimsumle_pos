@extends('components.layouts.pages')

@section('content')
<div class="min-h-screen bg-slate-50" x-data="posPage()">
    @include('pages.includes.sidebar')

    <div class="lg:pl-64">
        @include('pages.includes.header')

        <main class="p-4">
            <!-- Toast Notification -->
            @include('pages.includes.toast')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Product List -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Products Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        @forelse($products as $product)
                            <div x-show="isProductVisible('{{ addslashes($product->name) }}')"
                                @click="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock }})"
                                class="bg-white rounded-xl p-4 border border-slate-200 card-shadow cursor-pointer hover:border-blue-600 hover:border-2">
                                @if ($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                        class="w-full h-32 object-cover rounded-lg mb-3">
                                @else
                                    <div
                                        class="w-full h-32 bg-slate-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                                <h3 class="font-semibold text-slate-900 mb-1 text-sm">{{ $product->name }}</h3>
                                <p class="text-xs text-slate-500 mb-2">Stok: {{ $product->stock }}</p>
                                <p class="font-bold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="text-slate-500">Tidak ada produk tersedia</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Cart -->
                <div class="bg-white rounded-xl border border-slate-200 card-shadow h-fit sticky top-16">
                    <div class="p-4 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900">Keranjang</h3>
                    </div>

                    <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
                        <!-- Empty Cart State -->
                        <template x-if="cart.length === 0">
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <p class="text-slate-500 text-sm">Keranjang kosong</p>
                            </div>
                        </template>

                        <!-- Cart Items -->
                        <template x-for="(item, index) in cart" :key="item.id">
                            <div class="flex items-center space-x-3 p-3 bg-slate-50 rounded-lg">
                                <div class="flex-1">
                                    <p class="font-medium text-slate-900 text-sm" x-text="item.name"></p>
                                    <p class="text-xs text-slate-500" x-text="'Rp ' + formatRupiah(item.price)"></p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button @click="updateQuantity(index, 'decrease')"
                                        class="w-6 h-6 flex items-center justify-center bg-slate-200 hover:bg-slate-300 rounded">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="w-8 text-center font-semibold text-sm" x-text="item.quantity"></span>
                                    <button @click="updateQuantity(index, 'increase')"
                                        class="w-6 h-6 flex items-center justify-center bg-slate-200 hover:bg-slate-300 rounded">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                                <button @click="removeFromCart(index)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Cart Summary & Actions -->
                    <template x-if="cart.length > 0">
                        <div class="p-4 border-t border-slate-200 space-y-3">
                            <!-- Summary -->
                            <div class="space-y-2">
                                <div class="flex justify-between text-lg font-bold pt-2 border-t border-slate-100">
                                    <span>Total</span>
                                    <span x-text="'Rp ' + formatRupiah(total)"></span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="space-y-2 pt-3">
                                <button @click="openPaymentModal()"
                                    class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                    Bayar
                                </button>
                                <button @click="if(confirm('Yakin ingin reset transaksi?')) resetTransaction()"
                                    class="w-full border border-slate-300 text-slate-700 py-2 rounded-lg hover:bg-slate-50 transition-colors">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </main>
    </div>

    <!-- Payment Modal -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/50" @click="closePaymentModal()">
            </div>

            <div
                class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900">Pembayaran</h3>
                        <button type="button" @click="closePaymentModal()"
                            class="text-slate-400 hover:text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="bg-white px-6 py-4 space-y-4">
                    <!-- Total to Pay -->
                    <div class="bg-slate-50 p-4 rounded-lg">
                        <p class="text-sm text-slate-500 mb-1">Total Pembayaran</p>
                        <p class="text-3xl font-bold text-slate-900" x-text="'Rp ' + formatRupiah(total)"></p>
                    </div>

                    <!-- Payment Method -->
                    <fieldset>
                        <legend class="block text-sm font-medium text-slate-700 mb-2">Metode Pembayaran</legend>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" @click="paymentMethod = 'cash'"
                                :class="paymentMethod === 'cash' ? 'border-blue-600 bg-slate-50' : 'border-slate-200 hover:border-slate-300'"
                                class="p-3 border-2 rounded-lg text-center transition-all">
                                <svg class="w-6 h-6 mx-auto mb-1" :class="paymentMethod === 'cash' ? 'text-slate-900' : 'text-slate-400'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <span class="text-xs font-medium">Cash</span>
                            </button>
                            <button type="button" @click="paymentMethod = 'transfer'"
                                :class="paymentMethod === 'transfer' ? 'border-blue-600 bg-slate-50' : 'border-slate-200 hover:border-slate-300'"
                                class="p-3 border-2 rounded-lg text-center transition-all">
                                <svg class="w-6 h-6 mx-auto mb-1" :class="paymentMethod === 'transfer' ? 'text-slate-900' : 'text-slate-400'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                                <span class="text-xs font-medium">Transfer</span>
                            </button>
                            <button type="button" @click="paymentMethod = 'qris'"
                                :class="paymentMethod === 'qris' ? 'border-blue-600 bg-slate-50' : 'border-slate-200 hover:border-slate-300'"
                                class="p-3 border-2 rounded-lg text-center transition-all">
                                <svg class="w-6 h-6 mx-auto mb-1" :class="paymentMethod === 'qris' ? 'text-slate-900' : 'text-slate-400'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                    </path>
                                </svg>
                                <span class="text-xs font-medium">QRIS</span>
                            </button>
                        </div>
                    </fieldset>

                    <div>
                        <label for="paid" class="block text-sm font-medium text-slate-700 mb-2">
                            Pelanggan (Opsional)
                        </label>
                        <select x-model="customerId" class="w-full px-3 py-2 border border-slate-300 rounded-lg 
                        focus:ring-2 focus:ring-blue-600 focus:border-transparent text-sm">
                                    <option value="">Pilih Pelanggan</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>     
                    </div>
                    
                    <!-- Amount Paid -->
                    <div>
                        <label for="paid" class="block text-sm font-medium text-slate-700 mb-2">
                            Jumlah Dibayar
                        </label>
                        <input type="number" id="paid" x-model="paid" @input="calculateChange()" 
                            class="block w-full px-4 py-3 text-lg font-semibold border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                            placeholder="0"
                            @keydown="if(['E','e','+'].includes(event.key)) $event.preventDefault()">
                             
                    </div>

                    <!-- Change -->
                    <template x-if="change > 0">
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <p class="text-sm text-green-700 mb-1">Kembalian</p>
                            <p class="text-2xl font-bold text-green-900" x-text="'Rp ' + formatRupiah(change)"></p>
                        </div>
                    </template>

                    <template x-if="paid < 0">
                        <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                            <p class="text-sm text-red-700">Jumlah dibayar tidak boleh negatif</p>
                        </div>
                    </template>

                    <template x-if="paid > 0 && paid < total">
                        <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                            <p class="text-sm text-red-700">Jumlah pembayaran kurang dari total</p>
                        </div>
                    </template>
                </div>

                <div class="bg-slate-50 px-6 py-4 flex justify-end space-x-3">
                    <button type="button" @click="closePaymentModal()"
                        class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-100 transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="processPayment()" :disabled="loading || paid < total"
                        :class="paid < total ? 'opacity-50 cursor-not-allowed' : ''"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        <span x-show="!loading">Proses Pembayaran</span>
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
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="showSuccessModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/50"></div>
            <div
                class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <button @click="closeSuccessModal()" class="w-full flex items-center justify-end p-4">
                    <svg class="w-5 h-5 text-slate-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>               
</button>
                <div class="bg-white px-6 py-8 text-center">    
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">Pembayaran Berhasil!</h3>
                    <p class="text-slate-500 mb-4">Transaksi telah berhasil diproses</p>
                    <div class="bg-slate-50 p-4 rounded-lg mb-6">
                        <p class="text-sm text-slate-500 mb-1">No. Invoice</p>
                        <p class="text-xl font-bold text-slate-900" x-text="lastInvoice"></p>
                    </div>
                    <div class="flex flex-col gap-3">
                        <button type="button" @click="
                            let frame = document.getElementById('struk-frame');
                            frame.src = '/struk/' + selectedTransaction;
                            frame.onload = () => frame.contentWindow.print();"
                            class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            Cetak Struk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<iframe id="struk-frame" title="struk" style="display:none;"></iframe>
</div>

<style>[x-cloak] { display: none !important; }</style>

<script src="{{ asset('js/pos.js') }}"></script>
@endsection
