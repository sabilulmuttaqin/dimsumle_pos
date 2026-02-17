<?php

namespace Tests\Unit\Iterasi1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MultiLoginTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $kasir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'owner@test.com',
            'password' => bcrypt('password123'),
            'role' => 'owner',
        ]);
        $this->kasir = User::factory()->create([
            'email' => 'kasir@test.com',
            'password' => bcrypt('passwordkasir'),
            'role' => 'kasir',
        ]);
    }

    #[Test]
    public function login_multi_role_dengan_kredensial_valid()
    {
        $responseowner = $this->postJson('/login', [
            'email' => 'owner@test.com',
            'password' => 'password123',
        ]);
        $responsekasir = $this->postJson('/login', [
            'email' => 'kasir@test.com',
            'password' => 'passwordkasir',
        ]);

        $status_code_owner = $responseowner->getStatusCode();
        $status_code_kasir = $responsekasir->getStatusCode();
        $this->assertEquals('200', $status_code_owner);
        $this->assertEquals('200', $status_code_kasir);
    }

    #[Test]
    public function login_gagal_jika_kredensial_tidak_valid()
    {
        $responseOwner = $this->postJson('/login', [
            'email' => 'owner123@test.com',
            'password' => 'password123',
        ]);
        $responseKasir = $this->postJson('/login', [
            'email' => 'kasir123@test.com',
            'password' => 'passwordkasir',
        ]);
        $status_code_owner = $responseOwner->getStatusCode();
        $status_code_kasir = $responseKasir->getStatusCode();
        $this->assertEquals('422', $status_code_owner);
        $this->assertEquals('422', $status_code_kasir);
    }

    #[Test]
    public function login_gagal_jika_ada_field_kosong()
    {
        $responseOwner = $this->postJson('/login', [
            'email' => 'owner@test.com',
            'password' => '',
        ]);
        $responseKasir = $this->postJson('/login', [
            'email' => '',
            'password' => '',
        ]);
        $status_code_owner = $responseOwner->getStatusCode();
        $status_code_kasir = $responseKasir->getStatusCode();
        $this->assertEquals('422', $status_code_owner);
        $this->assertEquals('422', $status_code_kasir);
    }
    #[Test]
    public function login_gagal_jika_format_email_tidak_valid()
    {
        $responseOwner = $this->postJson('/login', [
            'email' => 'owner.com',
            'password' => 'password123',
        ]);
        $responseKasir = $this->postJson('/login', [
            'email' => 'kasir.com',
            'password' => 'passwordkasir',
        ]);
        $status_code_owner = $responseOwner->getStatusCode();
        $status_code_kasir = $responseKasir->getStatusCode();
        $this->assertEquals('422', $status_code_owner);
        $this->assertEquals('422', $status_code_kasir);
    }
}
