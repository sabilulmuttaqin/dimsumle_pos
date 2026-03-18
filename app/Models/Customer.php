<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\POS;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function pos(){
        return $this->HasMany(POS::class);
    }
}
