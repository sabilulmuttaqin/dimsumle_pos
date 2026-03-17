<?php

namespace Tests\Unit\Iterasi3;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


class CustomerTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $kasir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->kasir = User::factory()->create(['role' => 'kasir']);
    }

    #[Test]
    public function tambah_data_pelanggan_dengan_data_valid()
    {
        $response = $this->actingAs($this->owner)->postJson('/customers', [
            'name' => 'John Doe',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('customers', ['name' => 'John Doe']);
    }
    #[Test]
    public function gagal_tambah_pelanggan_jika_field_nama_kosong()
    {
        $response = $this->actingAs($this->owner)->postJson('/customers', [
            'name' => '',
        ]);

        $response->assertStatus(422);
    }
    #[Test]
    public function ubah_data_pelanggan_dengan_data_valid()
    {
        $customer = Customer::create(['name' => 'Lama']);

        $response = $this->actingAs($this->owner)->putJson("/customers/{$customer->id}", [
            'name' => 'Baru',
        ]);

        $status = $response->getStatusCode();
        $this->assertEquals('200', $status);
        $this->assertDatabaseHas('customers', ['name' => 'Baru']);
    }
    
    #[Test]
    public function hapus_data_pelanggan()
    {
        $customer = Customer::create(['name' => 'Hapus Saya']);

        $response = $this->actingAs($this->owner)->deleteJson("/customers/{$customer->id}");

        $status = $response->getStatusCode();
        $this->assertEquals('200', $status);
    }

}
