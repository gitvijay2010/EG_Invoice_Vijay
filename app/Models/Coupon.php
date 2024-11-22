<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'discount_value', 'expires_at'];

    public function getIsValidAttribute()
    {
        return now()->lte($this->expires_at);
    }
}
