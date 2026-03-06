<?php

namespace Tests\Unit\Iterasi2;

use App\Models\User;
use App\Models\Product;
use App\Models\POS;
use App\Models\TransactionDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kasir1 = User::factory()->create(['name' => 'Kasir Satu', 'role' => 'kasir']);
    }

    private function createTransaction(User $user, $date = null)
    {
        $product = Product::factory()->create(['price' => 10000]);
        
        $transaction = POS::create([
            'user_id'        => $user->id,
            'total'          => 10000,
            'paid_amount'    => 10000,
            'change_amount'  => 0,
            'payment_method' => 'cash',
            'invoice_number' => 'INV-TEST-' . uniqid(),
        ]);

        if ($date) {
            $transaction->timestamps = false;
            $transaction->created_at = $date;
            $transaction->updated_at = $date;
            $transaction->save();
            $transaction->timestamps = true;
        }

        TransactionDetail::create([
            'pos_id'     => $transaction->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'price'      => 10000,
            'subtotal'   => 10000,
        ]);

        return $transaction;
    }

    #[Test]
    public function dapat_melihat_riwayat_transaksi()
    {
        $this->createTransaction($this->kasir1);

        $response = $this->actingAs($this->kasir1)->get('/history');

        $response->assertStatus(200);
        
        $transactions = $response->original->getData()['transactions'];
        $this->assertEquals(1, $transactions->total());
    }

    #[Test]
    public function sistem_menampilkan_detail_transaksi()
    {
        $transaction = $this->createTransaction($this->kasir1);

        $response = $this->actingAs($this->kasir1)->getJson('/history/' . $transaction->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'transaction' => [
                'id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number,
                'user_name' => $this->kasir1->name,
                'total' => 10000,
                'paid_amount' => 10000,
                'change_amount' => 0,
                'payment_method' => 'Cash',
            ]
        ]);

    }

    #[Test]
    public function dapat_menghapus_transaksi()
    {
        $transaction = $this->createTransaction($this->kasir1);
        $response = $this->actingAs($this->kasir1)->deleteJson('/history/' . $transaction->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('pos', ['id' => $transaction->id]);
    }

    #[Test]
    public function sistem_dapat_memfilter_riwayat_transaksi_berdasarkan_tanggal()
    {
    $this->createTransaction($this->kasir1, '2026-05-01');
    $this->createTransaction($this->kasir1, '2026-05-03');

    $response = $this->actingAs($this->kasir1)->get(
        '/history?date_from=2026-05-01&date_to=2026-05-01'
    );

    $response->assertStatus(200);
    $transactions = $response->original->getData()['transactions'];
    $this->assertEquals(1, $transactions->total());
    }

    #[Test]
    public function sistem_menampilkan_total_pendapatan_transaksi()
    {
    $this->createTransaction($this->kasir1);
    $this->createTransaction($this->kasir1);
    $response = $this->actingAs($this->kasir1)->get('/history');
    $response->assertStatus(200);
    $totalRevenue = $response->original->getData()['summaryTotal'];
    $this->assertEquals(20000, $totalRevenue);
    }
    #[Test]
    public function sistem_gagal_menampilkan_riwayat_jika_rentang_tanggal_tidak_valid()
    {
    $this->createTransaction($this->kasir1, '2026-05-01');
    $this->createTransaction($this->kasir1, '2026-05-03');
    $response = $this->actingAs($this->kasir1)->get(
        '/history?date_from=2026-05-03&date_to=2026-05-01'
    );
    $response->assertStatus(302);
    }
}


