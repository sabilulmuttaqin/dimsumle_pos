<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $table = 'transaction_details';

    protected $fillable = [
        'pos_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function pos()
    {
        return $this->belongsTo(POS::class, 'pos_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
