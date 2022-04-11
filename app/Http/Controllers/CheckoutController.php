<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Checkout;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->get('user');
        $cart = Cart::with('products')->findOrFail((int) $request->get('cart_id'));

        $checkout = Checkout::create([
            'cart_id' => $cart->id,
            'status' => Checkout::STATUS_IN_PROGRESS,
            'total_price' => $cart->getTotalPrice()
        ]);

        return response()->json([
            'user' => $user,
            'checkout' => $checkout
        ]);
    }

    public function complete(int $id, Request $request)
    {
        $status = $request->get('status');
        $checkout = Checkout::findOrFail($id);
        $user = $request->get('user');

        if ($checkout->status === Checkout::STATUS_SUCCESS) {
            return response()->json('Already payed error');
        }

        if ($status === Checkout::STATUS_FAILED) {
            $checkout->update([
                'status' => Checkout::STATUS_FAILED
            ]);

            return response()->json('Payment error');
        }

        $user = User::create([
            'name' => $user['name'],
            'email' => $user['email'],
            'address' => $user['address'],
            'phone' => $user['phone'],
            'password' => 'hello_world'
        ]);

        $checkout->update([
            'user_id' => $user->id,
            'status' => Checkout::STATUS_SUCCESS
        ]);

        return response()->json($checkout->refresh());
    }
}
