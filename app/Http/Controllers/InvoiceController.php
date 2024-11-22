<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Coupon;

class InvoiceController extends Controller
{
    public function generateInvoice($orderId)
    {
        $order = Order::with(['user', 'orderItems.product'])->find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $customer = [
            'name' => $order->user->name,
            'email' => $order->user->email,
        ];

        $products = [];
        $subtotal = 0;

        foreach ($order->orderItems as $item) {
            $products[] = [
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->price,
                'subtotal' => $item->subtotal,
            ];

            $subtotal += $item->subtotal;
        }

        $discounts = [];
        $appliedDiscount = 0;

        $orderCoupons = \DB::table('order_coupons')
            ->where('order_id', $order->id)
            ->get();

        foreach ($orderCoupons as $coupon) {
            $appliedDiscount += $coupon->applied_discount;
            $discountDetails = Coupon::find($coupon->coupon_id);

            if ($discountDetails) {
                $discounts[] = [
                    'code' => $discountDetails->code,
                    'type' => $discountDetails->type,
                    'discount_value' => number_format($discountDetails->discount_value, 2, '.', ''),
                    'applied_discount' => number_format($coupon->applied_discount, 2, '.', ''),
                ];
            }
        }

        $tax = round(($subtotal - $appliedDiscount) * 0.07, 2);
        $total = round($subtotal - $appliedDiscount + $tax, 2);

        // Invoice Details
        $invoice = [
            'order_id' => $order->id,
            'customer' => $customer,
            'products' => $products,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'discounts' => $discounts,
            'applied_discount' => number_format($appliedDiscount, 2, '.', ''),
            'tax' => number_format($tax, 2, '.', ''),
            'total' => number_format($total, 2, '.', ''),
        ];

        return response()->json(['message' => 'Invoice generated successfully', 'invoice' => $invoice]);
    }
}
