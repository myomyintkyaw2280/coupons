<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'name',
        'query',
        'latitude',
        'longitude',
        'zoom',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_shops', 'shop_id', 'coupon_id');
    }
}
