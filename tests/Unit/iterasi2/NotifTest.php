<?php

namespace Tests\Unit\Iterasi2;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


class NotifTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Product $product;


    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['name' => 'owner', 'role' => 'owner']);
        $this->product = Product::factory()->create(['price' => 10000, 'stock' => 5]);
    }

    #[Test]
    public function muncul_notifikasi_stok_rendah()
    {
        $response = $this->actingAs($this->owner)->get('/dashboard');

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
        $response->assertSee('Stok Rendah');
    }
    #[Test]
public function tidak_muncul_notifikasi_stok_rendah_jika_stok_masih_mencukupi()
{
    $this->product->update(['stock' => 180]);

    $response = $this->actingAs($this->owner)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertDontSee('Stok Rendah');
}
}