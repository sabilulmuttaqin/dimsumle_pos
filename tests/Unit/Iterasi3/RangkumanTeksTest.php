<?php

namespace Tests\Unit\Iterasi3;

use App\Models\User;
use App\Models\Product;
use App\Models\POS;
use App\Models\TransactionDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Carbon\Carbon;

class RangkumanTeksTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $kasir;
    private User $kasir2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['name' => 'owner', 'role' => 'owner']);
        $this->kasir = User::factory()->create(['name' => 'kasir', 'role' => 'kasir']);
        $this->kasir2 = User::factory()->create(['name' => 'kasir', 'role' => 'kasir']);
    }

    private function createTransaction(User $user, $total, $date)
    {
        $product = Product::factory()->create(['price' => $total, 'stock' => 100]);

        $transaction = POS::create([
            'user_id'        => $user->id,
            'total'          => $total,
            'paid_amount'    => $total,
            'change_amount'  => 0,
            'payment_method' => 'cash',
            'invoice_number' => 'INV-TEST-' . uniqid(),
        ]);

        $transaction->timestamps = false;
        $transaction->updated_at = $date;
        $transaction->created_at = $date;
        $transaction->save();
        $transaction->timestamps = true;

        TransactionDetail::create([
            'pos_id'     => $transaction->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'price'      => $total,
            'subtotal'   => $total,
        ]);

        return $transaction;
    }

    #[Test]
    public function sistem_menghasilkan_ringkasan_berisi_perbandingan_omzet_profit_dan_jam_ramai()
    {
        $prevDate = Carbon::create(2026, 2, 15, 10, 0, 0);
        $this->createTransaction($this->kasir, 50000, $prevDate);

        $date1 = Carbon::create(2026, 3, 10, 14, 0, 0);
        $date2 = Carbon::create(2026, 3, 10, 14, 30, 0);
        $date3 = Carbon::create(2026, 3, 10, 9, 0, 0);

        $this->createTransaction($this->kasir, 30000, $date1);
        $this->createTransaction($this->kasir, 30000, $date2);
        $this->createTransaction($this->kasir, 20000, $date3);

        $response = $this->actingAs($this->owner)->getJson('/report/data?month=3&year=2026');

        $response->assertStatus(200);

        $insights = $response->json('insights');

        $this->assertArrayHasKey('salesComparison', $insights);
        $this->assertStringContainsString('Perbandingan Omzet', $insights['salesComparison']);
        $this->assertStringContainsString('meningkat', strtolower($insights['salesComparison']));
        $this->assertArrayHasKey('peakHourInsight', $insights);
        $this->assertStringContainsString('Jam ramai', $insights['peakHourInsight']);
        $this->assertStringContainsString('14:00', $insights['peakHourInsight']);
        $this->assertArrayHasKey('profitInsight', $insights);
        $this->assertStringContainsString('Profit', $insights['profitInsight']);
        $this->assertStringContainsString('Rp', $insights['profitInsight']);
    }
 #[Test]
    public function dapat_memfilter_data_berdasarkan_kasir()
    {
        $date1 = Carbon::create(2026, 3, 10, 19, 0, 0);
        $this->createTransaction($this->kasir, 50000, $date1);
        $date2 = Carbon::create(2026, 3, 10, 14, 30, 0);
        $this->createTransaction($this->kasir2, 30000, $date1);
        $response = $this->actingAs($this->owner)->getJson('/report/data?month=3&year=2026&cashier='.$this->kasir->id);

        $response->assertStatus(200);

        $insights = $response->json('insights');
        $this->assertStringContainsString('Jam ramai', $insights['peakHourInsight']);
        $this->assertStringContainsString('19:00', $insights['peakHourInsight']);
    }
#[Test]
public function sistem_tidak_menampilkan_ringkasan_jika_belum_ada_transaksi_di_periode()
{
    $response = $this->actingAs($this->owner)->getJson('/report/data?month=1&year=2023');

    $response->assertStatus(200);
    $response->assertJsonFragment(['insights' => []]);}
}
