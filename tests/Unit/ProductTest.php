<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;

class ProductTest extends TestCase
{
    /**
     * Test that a category has products.
     *
     * @return void
     */
    public function test_category_has_products()
    {
        $category = Category::factory()->create();

        $product = Product::factory()->create(['category' => $category->id]);

        $category->refresh();

        $this->assertTrue($category->products->contains($product));
        $this->assertCount(1, $category->products); // Ensure only one product is linked
    }

}
