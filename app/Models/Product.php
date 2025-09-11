<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Product extends Model
{
    use HasFactory;

    // Allow mass assignment
    protected $fillable = ['name', 'price'];

    /**
     * Many-to-Many relationship: Product ↔ Order
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product');
    }
}
