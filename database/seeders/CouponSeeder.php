<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Individual Product Coupon
        Coupon::create([
            'code' => 'PROD10',
            'type' => 'product', // Type: individual product
            'discount_value' => 10, // Discount value
            'product_id' => 1, // ID of the specific product (adjust based on your products)
            'expires_at' => now()->addDays(30), // Expiry date
        ]);

        // Flat Discount Coupon
        Coupon::create([
            'code' => 'FLAT50',
            'type' => 'flat', // Type: flat discount on the order
            'discount_value' => 50, // Discount value
            'product_id' => null, // No specific product, applies to the whole order
            'expires_at' => now()->addDays(30), // Expiry date
        ]);
    }
}
