<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $invoice }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }
        @page {
            size: 58mm auto;
            margin: 0;
        }
        #bt-status {
            font-size: 0.7rem;
            text-align: center;
            margin-top: 4px;
            min-height: 1rem;
        }
    </style>
</head>

<body class="bg-white font-mono">
    <div class="max-w-[58mm] mx-auto p-4">
        <!-- Header -->
        <div class="text-center flex flex-col justify-center items-center grayscale mb-4 pb-4 border-b-2 border-dashed border-slate-800">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-16 h-16 mx-auto mb-2">
            <h1 class="text-xl font-bold">Dimsum Lebih Enak</h1>
            <p class="text-xs">Telp: 0817-0371-7177</p>
        </div>

        <!-- Invoice Info -->
        <div class="mb-4 text-xs space-y-1">
            <div class="flex justify-between">
                <span>No Invoice:</span>
                <span class="font-semibold">{{ $invoice }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tanggal:</span>
                <span>{{ $date }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir:</span>
                <span>{{ $cashier }}</span>
            </div>
        </div>

        <!-- Items -->
        <div class="border-t-2 border-dashed border-slate-800 pt-3 mb-3">
            @foreach ($items as $item)
                <div class="mb-3">
                    <div class="font-semibold text-sm">{{ $item['name'] }}</div>
                    <div class="flex justify-between text-xs">
                        <span>{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                        <span class="font-semibold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="border-t-2 border-dashed border-slate-800 pt-3 text-xs space-y-1">
           

            <div class="flex justify-between font-bold text-base pt-2 ">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between pt-2">
                <span>Bayar ({{ $paymentMethod }}):</span>
                <span>Rp {{ number_format($paid, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between font-semibold">
                <span>Kembalian:</span>
                <span>Rp {{ number_format($change, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 pt-4 border-t-2 border-dashed border-slate-800 text-xs">
            <p class="mb-1">Terima Kasih Atas Kunjungan Anda</p>
            <p class="font-semibold">Selamat Berbelanja Kembali!</p>
        </div>

        <!-- Print Buttons -->
        <div class="no-print mt-6 flex flex-col gap-2">
            <div class="flex gap-2">
                <button id="btn-bluetooth" onclick="cetakBluetooth()"
                    class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors font-semibold text-sm">
                    Cetak Struk
                </button>
                <button onclick="window.close()"
                    class="flex-1 border border-slate-300 py-2 px-4 rounded-lg hover:bg-slate-50 transition-colors text-sm">
                    Tutup
                </button>
            </div>
            <p id="bt-status" class="text-slate-500"></p>
        </div>

        <script>
            window.strukturData = {
                invoice: '{{ $invoice }}',
                date: '{{ $date }}',
                cashier: '{{ $cashier }}',
                paymentMethod: '{{ $paymentMethod }}',
                total: '{{ number_format($total, 0, ',', '.') }}',
                paid: '{{ number_format($paid, 0, ',', '.') }}',
                change: '{{ number_format($change, 0, ',', '.') }}',
                items: {!! json_encode($items->map(fn($item) => [
                    'name'     => $item['name'],
                    'quantity' => $item['quantity'],
                    'price'    => number_format($item['price'], 0, ',', '.'),
                    'subtotal' => number_format($item['subtotal'], 0, ',', '.'),
                ])) !!},
            };
        </script>
        @vite('resources/js/bluetooth-print.js')
    </div>
</body>




</html>
