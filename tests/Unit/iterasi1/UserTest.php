<?php

namespace Tests\Unit\Iterasi1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


class UserTest extends TestCase
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
    public function tambah_kasir_dengan_data_yang_valid()
    {
        $response = $this->actingAs($this->owner)->postJson('/users', [
            'name' => 'Kasir Baru',
            'email' => 'kasirbaru@test.com',
            'password' => 'password123',
            'role' => 'kasir',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }

    #[Test]
    public function gagal_tambah_jika_ada_field_kosong()
    {
        $response = $this->actingAs($this->owner)->postJson("/users", [
            'name' => 'Nama Baru',
            'email' => '',
            'password' => 'password123',
            'role' => 'kasir',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }

    #[Test]
    public function gagal_tambah_jika_email_sudah_terdaftar()
    {
        User::factory()->create([
            'email' => 'kasir@test.com',
        ]);
        $response = $this->actingAs($this->owner)->postJson("/users", [
            'name' => 'Nama Baru',
            'email' => 'kasir@test.com',
            'password' => 'password123',
            'role' => 'kasir',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }

    #[Test]
    public function gagal_tambah_jika_email_tidak_valid()
    {
        $response = $this->actingAs($this->owner)->postJson("/users", [
            'name' => 'Nama Baru',
            'email' => 'kasir.com',
            'role' => 'kasir',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }

    #[Test]
    public function gagal_tambah_jika_password_kurang_dari_6_karakter()
    {
        $response = $this->actingAs($this->owner)->postJson("/users", [
            'name' => 'Nama Baru',
            'email' => 'kasir@gmail.com',
            'password' => '123',
            'role' => 'kasir',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }

    #[Test]
    public function ubah_data_kasir_dengan_data_valid()
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $response = $this->actingAs($this->owner)->putJson("/users/{$user->id}", [
            'name' => 'Budi',
            'email' => 'kasir@gmail.com',
            'password' => 'password123',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }


    #[Test]
    public function hapus_data_produk()
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($this->owner)->deleteJson("/users/{$user->id}");

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }
}
