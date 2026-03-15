<?php

namespace Tests\Unit\Iterasi3;

use App\Models\User;
use App\Models\Product;
use App\Models\POS;
use App\Models\TransactionDetail;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Carbon\Carbon;

class LaporanBulananTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $kasir1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner  = User::factory()->create(['name' => 'owner', 'role' => 'owner']);
        $this->kasir1 = User::factory()->create(['name' => 'Kasir 1', 'role' => 'kasir']);
    }

    private function createTransaction(User $user, $total = 10000, $date = null)
    {
        $product = Product::factory()->create(['price' => $total, 'stock' => 100]);

        $transaction = POS::create([
            'user_id'        => $user->id,
            'total'          => $total,
            'paid_amount'    => $total,
            'change_amount'  => 0,
            'payment_method' => 'cash',
            'invoice_number' => 'INV-' . uniqid(),
        ]);

        if ($date) {
            DB::table('pos')->where('id', $transaction->id)->update([
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $transaction->refresh();
        }

        TransactionDetail::create([
            'pos_id'     => $transaction->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'price'      => $total,
            'subtotal'   => $total,
        ]);

        return $transaction->load('details.product');
    }

    #[Test]
    public function sistem_menghitung_total_pemasukan_pengeluaran_dan_profit_dengan_benar()
    {
        $date = Carbon::create(2026, 3, 10, 12, 0, 0);
        $this->createTransaction($this->kasir1, 20000, $date);

        Expense::factory()->create([
            'user_id'      => $this->kasir1->id,
            'amount'       => 15000,
            'expense_date' => $date,
        ]);
        $response = $this->actingAs($this->owner)->getJson('/report/data?month=3&year=2026');

        $response->assertStatus(200);
        $response->assertJsonFragment(['profit'        => 5000]);
        $response->assertJsonFragment(['totalSales'    => 20000]);
        $response->assertJsonFragment(['totalExpenses' => 15000]);
    }

    #[Test]
    public function sistem_menampilkan_informasi_produk_terlaris()
    {
        $date        = Carbon::create(2026, 3, 10, 12, 0, 0);
        $transaction = $this->createTransaction($this->kasir1, 20000, $date);
        $product     = $transaction->details->first()->product;

        $response = $this->actingAs($this->owner)->getJson('/report/data?month=3&year=2026');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('topProducts'));
        $response->assertJsonFragment(['name' => $product->name]);
    }

    #[Test]
    public function dapat_memfilter_laporan_berdasarkan_tanggal()
    {
        $date = Carbon::create(2026, 3, 10, 12, 0, 0);
        $this->createTransaction($this->kasir1, 20000, $date);

        $response = $this->actingAs($this->owner)->getJson('/report/data?month=3&year=2026');
        $response->assertStatus(200);
        $response->assertJsonFragment(['totalSales'        => 20000]);
    }

    #[Test]
    public function dapat_memfilter_laporan_berdasarkan_kasir()
    {
        $kasir2 = User::factory()->create(['name' => 'Kasir 2', 'role' => 'kasir']);
        $date   = Carbon::create(2026, 3, 10, 12, 0, 0);

        $this->createTransaction($this->kasir1, 20000, $date);
        $this->createTransaction($kasir2, 50000, Carbon::create(2026, 3, 11, 12, 0, 0));

        $response = $this->actingAs($this->owner)->getJson('/report/data?month=3&year=2026&cashier=' . $this->kasir1->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(['totalTransactions' => 1]);
    }

    #[Test]
    public function sistem_tidak_menampilkan_laporan_jika_belum_ada_transaksi_di_periode_tersebut()
    {
        $response = $this->actingAs($this->owner)->getJson('/report/data?month=1&year=2023');

        $response->assertStatus(200);
        $response->assertJsonFragment(['totalSales'        => 0]);
    }
}