<?php

namespace Tests\Unit\Iterasi2;

use App\Models\User;
use App\Models\Product;
use App\Models\POS;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class POSTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kasir = User::factory()->create(['role' => 'kasir']);
    }
 
    #[Test]
    public function dapat_mencatat_transaksi_dengan_data_yang_valid(){
        $product = Product::factory()->create(['price' => 10000, 'stock' => 10]);

        $response = $this->actingAs($this->kasir)->postJson('/pos', [
            'cart' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                    'price' => 10000,
                    'subtotal' => 10000,
                ],
            ],
            'paid_amount' => 50000,
            'payment_method' => 'cash',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }
     #[Test]
    public function sistem_menghitung_total_transaksi_dengan_benar()
    {
        $product = Product::factory()->create(['price' => 10000, 'stock' => 10]);

        $response = $this->actingAs($this->kasir)->postJson('/pos', [
            'cart' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                    'price' => 10000,
                    'subtotal' => 10000,
                ],
            ],
            'paid_amount' => 50000,
            'payment_method' => 'cash',
        ]);
        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
        $transaction = POS::latest()->first();
        $this->assertEquals(10000, $transaction->total);
    }
    #[Test]
    public function sistem_menghitung_kembalian_dengan_benar()
    {
        $product = Product::factory()->create(['price' => 10000, 'stock' => 10]);

        $response = $this->actingAs($this->kasir)->postJson('/pos', [
            'cart' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                    'price' => 10000,
                    'subtotal' => 10000,
                ],
            ],
            'paid_amount' => 50000,
            'payment_method' => 'cash',
        ]);
        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
        $transaction = POS::latest()->first();
        $this->assertEquals(40000, $transaction->change_amount);
    }

    #[Test]
    public function transaksi_gagal_jika_jumlah_dibayar_kosong()
    {
        $product = Product::factory()->create(['price' => 10000, 'stock' => 10]);

        $response = $this->actingAs($this->kasir)->postJson('/pos', [
            'cart' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                    'price' => 10000,
                    'subtotal' => 10000,
                ],
            ],
            'paid_amount' => '',
            'payment_method' => 'cash',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }
    #[Test]
    public function sistem_menolak_transaksi_jika_jumlah_dibayar_kurang()
    {
        $product = Product::factory()->create(['price' => 10000, 'stock' => 10]);

        $response = $this->actingAs($this->kasir)->postJson('/pos', [
            'cart' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                    'price' => 10000,
                    'subtotal' => 10000,
                ],
            ],
            'paid_amount' => 5000,
            'payment_method' => 'cash',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }

    #[Test]
    public function sistem_tidak_menerima_input_pembayaran_negatif()
    {
        $product = Product::factory()->create(['price' => 10000, 'stock' => 10]);

        $response = $this->actingAs($this->kasir)->postJson('/pos', [
            'cart' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                    'price' => 10000,
                    'subtotal' => 10000,
                ],
            ],
            'paid_amount' => -5000,
            'payment_method' => 'cash',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);

    }}
