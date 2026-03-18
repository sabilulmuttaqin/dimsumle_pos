<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TransactionDetail;
use App\Models\Customer;

class POS extends Model
{
    use HasFactory;

    protected $table = 'pos';

    protected $fillable = [
        'invoice_number',
        'user_id',
        'total',
        'paid_amount',
        'change_amount',
        'payment_method',
        'customer_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'pos_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->invoice_number)) {
                $transaction->invoice_number =
                    "INV-" .
                    date("Ymd") .
                    "-" .
                    str_pad(
                        static::whereDate("updated_at", today())->count() + 1,
                        4,
                        "0",
                        STR_PAD_LEFT,
                    );
            }
    });
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
