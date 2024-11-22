<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'quantity',
        'price',
        'description',
        'category', // Foreign key
    ];

    // A product belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category', 'id');
    }
}
