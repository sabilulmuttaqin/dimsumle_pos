<?php

namespace Tests\Unit\Iterasi2;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class StrukTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasir = User::factory()->create(['role' => 'kasir']);
        $this->product = Product::factory()->create(['price' => 10000,'stock' => 10]);
    }

    #[Test]
    public function sistem_menampilkan_struk_setelah_transaksi()
    {
        $transaction = $this->actingAs($this->kasir)->postJson('/pos', [
            'cart' => [
                [
                    'id' => $this->product->id,
                    'quantity' => 1,
                    'price' => 10000,
                    'subtotal' => 10000,
                ],
            ],
            'paid_amount' => 10000,
            'payment_method' => 'cash',
        ])->json();

        $response = $this->actingAs($this->kasir)->get('/struk/' . $transaction['id']);

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }

    #[Test]
    public function sistem_tidak_menampilkan_struk_jika_transaksi_tidak_ditemukan()
    {
        $response = $this->actingAs($this->kasir)->get('/struk/99999');
        $response->assertStatus(404);
    }
}