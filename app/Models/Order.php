<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Import related models
use App\Models\User;
use App\Models\Product;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
    ];

    /**
     * Inverse of User → Orders (Many-to-One)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Many-to-Many: Order ↔ Product
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product');
    }
}
