<?php

namespace Tests\Unit\Iterasi3;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


class POSCustomerTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kasir = User::factory()->create(['role' => 'kasir']);
    }

   #[Test]
    public function dapat_membuat_transaksi_dengan_data_pelanggan()
    {
        $product = Product::factory()->create(['price' => 10000, 'stock' => 10]);
        $customer = Customer::factory()->create(['name'=>"pelanggan"]);

        $response = $this->actingAs($this->kasir)->postJson('/pos', [
            'cart' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                    'price' => 10000,
                    'subtotal' => 10000,
                ],
            ],
            'paid_amount' => 10000,
            'payment_method' => 'cash',
            'customer_id'=>$customer->id,
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }

    #[Test]
    public function dapat_membuat_transaksi_tanpa_pelanggan()
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
            'paid_amount' => 10000,
            'payment_method' => 'cash',
            'customer_id'=> null,
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }
}
