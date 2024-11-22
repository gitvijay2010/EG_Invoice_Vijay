<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class OrderController extends Controller
{
    public function payNow(Request $request)
    {
        $user = $request->user();
        $cartItems = Cart::where('user_id', $user->id)->where('status', 0)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // Validate payment type
        $validatedData = $request->validate([
            'payment_type' => 'required|in:credit,cash,paypal',
        ]);
        $paymentType = $validatedData['payment_type'];

        // Calculate totals
        $subtotal = $cartItems->sum(fn($cartItem) => $cartItem->quantity * $cartItem->price);

        $flatDiscount = 0;
        $productDiscounts = [];

        // Fetch applied coupons from the cart (ensure these are the correct relationships)
        $couponIds = $cartItems->pluck('coupon_id')->unique();
        $coupons = Coupon::whereIn('id', $couponIds)->get();
        // \Log::info($coupons);


        foreach ($coupons as $coupon) {
            // Apply flat discount if it's a flat coupon
            if ($coupon->type === 'flat') {
                $flatDiscount += $coupon->discount_value;
            } elseif ($coupon->type === 'product') {
                foreach ($cartItems as $cartItem) {
                    if ($cartItem->product_id == $coupon->product_id) {
                        $productDiscounts[$cartItem->id] = round(
                            $cartItem->quantity * $cartItem->price * ($coupon->discount_value / 100), 
                            2
                        );
                    }
                }
            }
        }

        $totalProductDiscount = array_sum($productDiscounts);
        $discount = $flatDiscount + $totalProductDiscount;

        // Calculate tax (7%)
        $tax = round(($subtotal - $discount) * 0.07, 2);
        $finalTotal = round($subtotal - $discount + $tax, 2);

        // Create Order and return the actual Order model
        $order = $this->createOrder($user, $cartItems, [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $finalTotal,
            'payment_type' => $paymentType,
        ]);

        // Process payment here (e.g., PayPal/Card integration)
        $paymentSuccessful = true;

        if ($paymentSuccessful) {
            foreach ($cartItems as $cartItem) {
                // Update product quantity and cart status
                $product = $cartItem->product;

                if ($product->quantity < $cartItem->quantity) {
                    return response()->json([
                        'message' => "Not enough stock for product: {$product->name}",
                    ], 400);
                }

                $product->decrement('quantity', $cartItem->quantity); // Decrease stock
                $cartItem->update(['status' => 1]); // Mark cart item as purchased
            }

            // Save `order_coupons` table
            foreach ($coupons as $coupon) {
                foreach ($cartItems as $cartItem) {
                    $appliedDiscount = 0;
                    if ($coupon->type === 'flat') {
                        $appliedDiscount = $coupon->discount_value;
                    } elseif ($coupon->type === 'product' && $cartItem->product_id == $coupon->product_id) {
                        $appliedDiscount = round(
                            $cartItem->quantity * $cartItem->price * ($coupon->discount_value / 100), 
                            2
                        );
                    }

                    DB::table('order_coupons')->insert([
                        'order_id' => $order->id, // Now it works because $order is the Order model
                        'coupon_id' => $coupon->id,
                        'applied_discount' => $appliedDiscount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'message' => 'Order created and payment processed successfully',
                'order' => $order,
            ]);
        }

        return response()->json(['message' => 'Payment failed'], 400);
    }


    // Create an order from cart items
    public function createOrder($user, $cartItems, $orderData)
    {
        // Start a database transaction
        \DB::beginTransaction();
        
        try {
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $orderData['subtotal'],
                'discount' => $orderData['discount'],
                'tax' => $orderData['tax'],
                'total' => $orderData['total'],
                'payment_type' => $orderData['payment_type'],
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->sub_total,
                    'tax' => $cartItem->tax,
                ]);
            }

            \DB::commit();

            return $order;

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            \DB::rollBack();
            return response()->json(['error' => 'Failed to create order', 'message' => $e->getMessage()], 500);
        }
    }

    public function getUserOrders(Request $request)
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)->with('orderItems')->get(); // Get orders with related items
        
        return response()->json([
            'orders' => $orders
        ]);
    }

    public function getOrderDetails($orderId)
    {
        $order = Order::with('orderItems')->findOrFail($orderId); // Load order with related items
        return response()->json([
            'order' => $order
        ]);
    }
}
