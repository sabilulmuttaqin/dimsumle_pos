<?php

namespace Tests\Unit\Iterasi3;

use App\Http\Controllers\DashboardController;
use App\Models\Product;
use App\Models\User;
use App\Models\POS;
use App\Models\TransactionDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SendDailyReportTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $kasir1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner  = User::factory()->create(['role' => 'owner']);
        $this->kasir1 = User::factory()->create(['role' => 'kasir']);
    }

    private function createTodayTransaction(?int $userId = null): POS
    {
        $product = Product::factory()->create(['name' => 'Dimsum Mentai', 'price' => 10000, 'stock' => 100]);

        $transaction = POS::create([
            'user_id'        => $userId ?? $this->kasir1->id,
            'total'          => 10000,
            'paid_amount'    => 10000,
            'change_amount'  => 0,
            'payment_method' => 'cash',
            'invoice_number' => 'INV-TEST-' . uniqid(),
        ]);

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
    public function laporan_berhasil_dikirim_jika_credentials_telegram_benar(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $this->createTodayTransaction();

        config(['services.telegram.token'   => 'test-token']);
        config(['services.telegram.chat_id' => 'test-chat-id']);

        $this->artisan('report:daily')
            ->expectsOutput('Report sent successfully to Telegram.');
    }

    #[Test]
    public function laporan_gagal_dikirim_jika_credentials_telegram_salah(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response([
                'ok'          => false,
                'description' => 'Unauthorized',
            ], 401),
        ]);

        $this->createTodayTransaction();

        config(['services.telegram.token'   => 'invalid-token']);
        config(['services.telegram.chat_id' => 'invalid-chat-id']);

        $this->artisan('report:daily')
            ->expectsOutputToContain('Failed to send report.')
            ->assertSuccessful();
    }

    #[Test]
    public function laporan_berisi_omzet_transaksi_dan_metode_pembayaran(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $this->createTodayTransaction();

        config(['services.telegram.token'   => 'test-token']);
        config(['services.telegram.chat_id' => 'test-chat-id']);

        $this->artisan('report:daily')
            ->expectsOutput('Report sent successfully to Telegram.')
            ->assertSuccessful();

        Http::assertSent(function ($request) {
            $text = $request['text'];
            return str_contains($text, 'Total Omzet: Rp 10.000')
                && str_contains($text, 'Dimsum Mentai x1 - Rp 10.000 (CASH)');
        });
    }

    #[Test]
    public function dashboard_menampilkan_data_laporan_penjualan_harian_berdasarkan_kasir(): void
    {
        $kasir2 = User::factory()->create(['role' => 'kasir']);

        $this->createTodayTransaction();
        $this->createTodayTransaction();

        $request = new Request(['kasir_id' => $this->kasir1->id]);
        $controller = new DashboardController();
        $view = $controller->index($request);
        $data = $view->getData();

        $this->assertEquals(2, $data['todayTransactions']);
        $this->assertEquals(20000, $data['todayRevenue']);
    }
}