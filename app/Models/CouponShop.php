<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponShop extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'shop_id',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
