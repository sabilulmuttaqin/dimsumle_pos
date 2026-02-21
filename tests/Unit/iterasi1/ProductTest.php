<?php

namespace Tests\Unit\Iterasi1;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['role' => 'owner']);
    }

    
    #[Test]
    public function tambah_produk_dengan_data_yang_valid()
    {
        Storage::fake('public');

        $response = $this->actingAs($this->owner)->postJson('/products', [
            'name' => 'Dimsum Ayam',
            'price' => 15000,
            'stock' => 50,
            'image' => UploadedFile::fake()->image('dimsum.jpg', 200, 200),
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }

    #[Test]
    public function gagal_tambah_produk_jika_ada_field_kosong_selain_gambar()
    {
        Storage::fake('public');

        $response = $this->actingAs($this->owner)->postJson('/products', [
            'name' => '',
            'price' => '',
            'stock' => '',
            'image' => UploadedFile::fake()->image('dimsum.jpg', 200, 200),
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }
    #[Test]
    public function gagal_tambah_produk_jika_field_stok_dan_harga_negatif(){
        Storage::fake('public');
        $response = $this->actingAs($this->owner)->postJson('/products', [
            'name' => 'dimsum',
            'price' => -5000,
            'stock' => -10,
            'image' => UploadedFile::fake()->image('dimsum.jpg', 200, 200),
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }

     #[Test]
    public function gagal_tambah_produk_jika_field_gambar_lebih_dari_2_MB(){
        Storage::fake('public');
        $response = $this->actingAs($this->owner)->postJson('/products', [
            'name' => 'dimsum',
            'price' => -5000,
            'stock' => -10,
            'image' => UploadedFile::fake()->image('dimsum.jpg', 2100, 2100),
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }
    #[Test]
    public function ubah_data_produk_dengan_data_yang_valid(){
        Storage::fake('public');
        $product = Product::factory()->create();
        $response = $this->actingAs($this->owner)->postJson("/products/{$product->id}", [
            '_method' => 'PUT',
            'name' => 'Nama Baru',
            'price' => 20000,
            'stock' => 10,
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }

    #[Test]
    public function hapus_data_produk(){
        $product = Product::factory()->create();

        $response = $this->actingAs($this->owner)->deleteJson("/products/{$product->id}");

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }
}
