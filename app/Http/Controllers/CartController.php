<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(int $id)
    {
        $cart = Cart::with(['products'])->findOrFail($id);

        return response()->json($cart);
    }

    public function store(Request $request)
    {
        $cart = Cart::create($request->all());

        return response()->json($cart);
    }

    public function addProduct(int $id, int $productId, Request $request)
    {
        $cart = Cart::with('products')->findOrFail($id);
        $product = Product::findOrFail($productId);
        $quantity = (int) $request->get('quantity');

        if (!$product->is_in_stock) {
            return response()->json('The product is not in stock', 400);
        }

        if ($cartProduct = $cart->products->where('id', $productId)->first()) {
            if ($quantity) {
                $cartProduct->pivot->update([
                    'quantity' => $quantity
                ]);
            } else {
                $cartProduct->pivot->increment('quantity');
            }
        } else {
            $cart->products()->attach($product, [
                'quantity' => $quantity
            ]);
        }

        return response()->json($cart->refresh());
    }

    public function removeProduct(int $id, int $productId)
    {
        $cart = Cart::with('products')->findOrFail($id);
        $product = Product::findOrFail($productId);

        $cart->products()->detach($product->id);

        return response()->json($cart->refresh());
    }
}
