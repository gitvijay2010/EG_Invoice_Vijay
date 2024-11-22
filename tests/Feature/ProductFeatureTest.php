<?php
namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use DB;

class ProductFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testProductCanBeCreated()
    {
        // Act as an authenticated admin
        $admin = $this->actingAsAuthenticatedAdmin();

        // Create a category for the product
        $category = Category::factory()->create();

        // Prepare the product data (payload)
        $payload = [
            'name' => 'Test Product',
            'quantity' => 10,
            'price' => 90,
            'description' => 'This is a test product',
            'category' => $category->id,
        ];

        $response = $this->post('/admin/products', $payload);

        // status code 302
        $response->assertStatus(302);

        // Assert the product
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'quantity' => 10,
            'price' => 90,
            'description' => 'This is a test product',
            'category' => $category->id,
        ]);
    }

    public function testProductCanBeAddedToCart()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        // \Log::info('Token:', ['token' => $token]); 

        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00,
            'quantity' => 10
        ]);

        $data = [
            'product_id' => $product->id,
            'quantity' => 2
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/cart', $data);
        // dd($response->getContent());

        $response->assertStatus(201);

        $this->assertDatabaseHas('cart', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }
    
    
    public function testAuthenticatedAdminCanSeeProductList()
    {
        $admin = $this->actingAsAuthenticatedAdmin();

        $category = Category::factory()->create();
        Product::factory()->create(['category' => $category->id]);

        $response = $this->get('/admin/products');
        // dd($response->getContent());

        $response->assertStatus(200);

        $response->assertSee($category->id);
    }


    //Test to update a product
    public function testProductCanBeUpdated()
    {
        $admin = $this->actingAsAuthenticatedAdmin();

        $category = Category::factory()->create();
        $product = Product::factory()->create(['category' => $category->id]);

        $updatedData = [
            'name' => 'Updated Product Name',
            'quantity' => 20,
            'price' => 150.00,
            'description' => 'Updated product description',
            'category' => $category->id,
        ];

        $response = $this->put("/admin/products/{$product->id}", $updatedData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('products', ['name' => 'Updated Product Name']);
    }
    
    // Test to delete a product
    public function testProductCanBeDeleted()
    {
        $admin = $this->actingAsAuthenticatedAdmin();

        $category = Category::factory()->create();
        $product = Product::factory()->create(['category' => $category->id]);

        $response = $this->delete("/admin/products/{$product->id}");
        // dd($response->getContent());

        $response->assertStatus(302);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
    
    // Test if a guest cannot access the product routes
    public function testGuestCannotAccessProductRoutes()
    {
        $response = $this->get('/admin/products');

        // Assert that the guest is redirected to login
        $response->assertRedirect('/admin/enter');
    }

    public function testAddToCart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);
        $subTotal = round(($product->price * $product->quantity), 2);
        $cartItem = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
            'sub_total' => $subTotal,
            'discount' => 0,
            'tax' => round($subTotal * 0.07, 2),
        ]);
        // dd($cartItem);

        $response = $this->actingAs($user)
                         ->postJson('/api/checkout');

        $response->assertStatus(200);
        $this->assertDatabaseHas('cart', [
            'user_id' => $user->id,
            'sub_total' => $subTotal,
        ]);
    }

    public function testPayNow()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);

        $subTotal = round(($product->price * 1), 2);

        $cartItem = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
            'sub_total' => $subTotal,
            'discount' => 0,
            'tax' => round($subTotal * 0.07, 2),
        ]);

        $response = $this->actingAs($user)
                         ->postJson('/api/paynow', [
                             'payment_type' => 'credit'
                         ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_type' => 'credit',
            'subtotal' => $subTotal
        ]);
    }

    public function testGenerateInvoiceOrder()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);

        $subtotal = 100.00;
        $tax = round($subtotal * 0.07, 2);
        $total = $subtotal + $tax;

        // Create the order
        $order = Order::create([
            'user_id' => $user->id,
            'subtotal' => $subtotal,
            'total' => $total,
            'tax' => $tax, // Include tax
            'discount' => '0.00',
            'payment_type' => 'credit'
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
            'subtotal' => $product->price,
            'tax' => $order->tax,
        ]);

        $response = $this->actingAs($user)
                         ->getJson('/api/invoice/' . $order->id);

        $response->assertStatus(200);
        $response->assertJson([
            'invoice' => [
                'order_id' => $order->id,
                'customer' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'products' => [
                    [
                        'product_name' => $product->name,
                        'quantity' => 1,
                        'unit_price' => '100.00',
                        'subtotal' => '100.00',
                    ]
                ],
                'subtotal' => '100.00',
                'tax' => '7.00',
                'total' => '107.00', 
                'discounts' => [],
                'applied_discount' => '0.00',
            ]
        ]);
    }


}
