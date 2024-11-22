<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart2(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    public function addToCart(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string|exists:coupons,code',
        ]);

        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Check if the product is in stock
        if ($product->quantity < $request->quantity) {
            return response()->json(['message' => 'Out of stock'], 400);
        }

        // Calculate price, discount, subtotal, and tax
        $price = $product->price;
        $quantity = $request->quantity;
        $subTotal = $price * $quantity;
        $discount = 0;
        $tax = 0;
        
        $coupon_id = null;
        // Check if coupon is applied
        if ($request->has('coupon_code')) {
            $coupon = $this->validateCoupon($request->coupon_code, $subTotal);
            $coupon_id = $coupon->id;
            $discount = $this->applyCoupon($coupon, $product, $quantity);
            $subTotal = round($subTotal - $discount, 2);
            $tax = round($subTotal * 0.07, 2);  // Assume 7% tax
        }
        $finalTotal = $subTotal - $discount + $tax;

        $subTotal   = number_format($subTotal, 2, '.', '');
        $discount   = number_format($discount, 2, '.', '');
        $tax        = number_format($tax, 2, '.', '');
        $finalTotal = number_format($finalTotal, 2, '.', '');

        // Create the cart item
        $cartItem = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $price,
            'sub_total' => $subTotal,
            'discount' => $discount,
            'coupon_id' => $coupon_id,
            'tax' => $tax,
            'status' => 0, // Pending
        ]);

        return response()->json([
            'message' => 'Item added to cart successfully',
            'cart_item' => $cartItem,
            'finalTotal' => $finalTotal
        ], 201);
    }

    public function checkout(Request $request)
    {
        $cartItems = Cart::where('user_id', $request->user()->id)->where('status', 0)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $subtotal = $cartItems->sum(function ($cartItem) {
            return $cartItem->quantity * $cartItem->price;
        });

        $discount = $cartItems->sum(function ($cartItem) {
            return $cartItem->discount;
        });

        $tax = round(($subtotal - $discount) * 0.07, 2);
        $finalTotal = round($subtotal - $discount + $tax, 2);

        // Return the calculated totals
        return response()->json([
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'discount' => number_format($discount, 2, '.', ''),
            'tax' => number_format($tax, 2, '.', ''),
            'finalTotal' => number_format($finalTotal, 2, '.', '')
        ]);
    }

    private function validateCoupon($couponCode, $subTotal)
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        if (!$coupon) {
            throw new \Exception('Coupon not found');
        }
        if (now()->gt($coupon->expires_at)) {
            throw new \Exception('Coupon has expired');
        }
        return $coupon;
    }

    private function validateCoupons(array $couponCodes)
    {
        $coupons = Coupon::whereIn('code', $couponCodes)->get();
        if (!$coupon) {
            throw new \Exception('Coupon not found');
        }
        foreach ($coupons as $coupon) {
            if (now()->gt($coupon->expires_at)) {
                throw new \Exception("Coupon {$coupon->code} has expired");
            }
        }
        return $coupons;
    }


    private function applyCoupon($coupon, $product, $quantity = null)
    {
        if ($coupon->type == 'flat') {
            return $coupon->discount_value;
        } elseif ($coupon->type == 'product' && $coupon->product_id == $product->id) {
            return round($product->price * $quantity * ($coupon->discount_value / 100), 2);
        }
        return 0;
    }

}
