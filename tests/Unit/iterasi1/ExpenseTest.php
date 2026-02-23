<?php

namespace Tests\Unit\Iterasi1;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['role' => 'owner']);
    }

    #[Test]
    public function tambah_pengeluaran_dengan_data_yang_valid()
    {
        $response = $this->actingAs($this->owner)->postJson('/expenses', [
            'amount' => 50000,
            'description' => 'Beli alat tulis',
            'expense_date' => '2026-04-26',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }

    #[Test]
    public function gagal_tambah_pengeluaran_jika_jumlah_negatif()
    {
        $response = $this->actingAs($this->owner)->postJson('/expenses', [
            'amount' => -50000,
            'description' => '',
            'expense_date' => '2026-04-26',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }
    #[Test]
    public function gagal_tambah_pengeluaran_jika_ada_field_kosong(){
        $response = $this->actingAs($this->owner)->postJson('/expenses', [
            'amount' => '',
            'description' => 'Beli alat tulis',
            'expense_date' => '2026-04-26',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('422', $status_code);
    }


    #[Test]
    public function sistem_dapat_memfilter_pengeluaran_berdasarkan_tanggal()
    {
    Expense::factory()->create(['user_id' => $this->owner->id, 'expense_date' => '2026-03-01']);
    Expense::factory()->create(['user_id' => $this->owner->id, 'expense_date' => '2026-03-03']);

    $response = $this->actingAs($this->owner)->get(
        '/expenses?date_from=2026-03-01&date_to=2026-03-01'
    );

    $response->assertStatus(200);
    $expenses = $response->original->getData()['expenses'];
    $this->assertEquals(1, $expenses->total());
    }

    #[Test]
    public function sistem_gagal_menampilkan_pengeluaran_jika_rentang_tanggal_tidak_valid()
    {
    Expense::factory()->create(['user_id' => $this->owner->id, 'expense_date' => '2026-05-01']);
    Expense::factory()->create(['user_id' => $this->owner->id, 'expense_date' => '2026-05-03']);
    $response = $this->actingAs($this->owner)->get(
        '/expenses?date_from=2026-05-03&date_to=2026-05-01'
    );
    $response->assertStatus(302);
    }

    #[Test]
    public function ubah_pengeluaran_dengan_data_valid()
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->owner->id,
            'amount' => 50000,
        ]);

        $response = $this->actingAs($this->owner)->putJson("/expenses/{$expense->id}", [
            'amount' => 75000,
            'description' => 'Updated desc',
            'expense_date' => '2026-04-26',
        ]);

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }

    #[Test]
    public function hapus_data_pengeluaran()
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->owner)->deleteJson("/expenses/{$expense->id}");

        $status_code = $response->getStatusCode();
        $this->assertEquals('200', $status_code);
    }
}
