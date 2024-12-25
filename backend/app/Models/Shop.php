<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category',
    ];


    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}
