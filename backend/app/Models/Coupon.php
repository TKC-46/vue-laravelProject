<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shop_id',
        'code',
        'description',
        'expiry_date',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
