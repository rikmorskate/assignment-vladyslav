<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_products')
            ->withPivot([
                'quantity'
            ]);
    }

    public function getTotalPrice()
    {
        $total = 0;

        $this->products->map(function (Product $product) use (&$total) {
            $total += $product->pivot->quantity * $product->price;
        });

        return $total;
    }
}
