<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'name',
        'description',
        'discount_type',
        'amount',
        'image_url',
        'code',
        'start_datetime',
        'end_datetime',
        'coupon_type',
        'used_count',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'coupon_shops', 'coupon_id', 'shop_id');
    }
}
