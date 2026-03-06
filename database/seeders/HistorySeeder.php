<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\POS;
use App\Models\TransactionDetail;

class HistorySeeder extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'kasir@gmail.com')->first();
        
        $product = Product::inRandomOrder()->first();
        if (!$product) {
            $product = Product::factory()->create(['price' => 13000]);
        }

        // Transaksi 1: 28 Februari 2026
        $transaction1 = POS::create([
            'user_id'        => $user->id,
            'total'          => 13000,
            'paid_amount'    => 15000,
            'change_amount'  => 2000,
            'payment_method' => 'cash',
            'invoice_number' => 'INV-20260228-0001',
        ]);
        
        $transaction1->created_at = '2026-02-28 10:00:00';
        $transaction1->updated_at = '2026-02-28 10:00:00';
        $transaction1->save();

        TransactionDetail::create([
            'pos_id'     => $transaction1->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'price'      => 13000,
            'subtotal'   => 13000,
        ]);

        // Transaksi 2: 01 Maret 2026
        $transaction2 = POS::create([
            'user_id'        => $user->id,
            'total'          => 26000,
            'paid_amount'    => 50000,
            'change_amount'  => 24000,
            'payment_method' => 'cash',
            'invoice_number' => 'INV-20260301-0001',
        ]);
        
        $transaction2->created_at = '2026-03-01 10:00:00';
        $transaction2->updated_at = '2026-03-01 10:00:00';
        $transaction2->save();

        TransactionDetail::create([
            'pos_id'     => $transaction2->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'price'      => 13000,
            'subtotal'   => 26000,
        ]);
    }
}
