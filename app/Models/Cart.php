<?php
// app/Models/Cart.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart'; // Specify the table name (optional, if it matches the model name)

    protected $fillable = [
        'user_id', 
        'product_id', 
        'quantity', 
        'price', 
        'sub_total',
        'discount',
        'tax',
        'status',
        'coupon_id'
    ];

    // Define relationships
    public function product()
    {
        return $this->belongsTo(Product::class); // Cart belongs to a product
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Cart belongs to a user
    }

    // Optionally, you can add an accessor for calculating total price
    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->price;
    }
}
