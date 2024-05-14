<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_products(): void
    {
        // Arrange
        Category::factory()->create();
        Supplier::factory()->create();
        Product::factory(5)->create();

        // Act
        $response = $this->get(route('products'));

        // Assert
        $response->assertStatus(200)->assertJsonCount(5, 'products');
    }
    
    public function test_filter_products(): void
    {
        // Arrange
        Category::factory()->create();
        Supplier::factory()->create();
        $product = Product::factory()->create();

        // Act
        $response = $this->post(route('filter_products'), [
            'category_id' => $product->category->id,
            'supplier_id' => $product->supplier->id,
        ]);

        // Assert
        $response->assertStatus(200)->assertJsonStructure(['products']);
    }

    public function test_show_product(): void
    {
        // Arrange
        Category::factory()->create();
        Supplier::factory()->create();
        $product = Product::factory()->create();

        // Act
        $response = $this->get(route('product', $product->id));

        // Assert
        $response->assertStatus(200)->assertJsonStructure(['product']);
    }

    public function test_show_product_category(): void
    {
        // Arrange
        Category::factory()->create();
        Supplier::factory()->create();
        $product = Product::factory()->create();

        // Act
        $response = $this->get(route('product_category', $product->id));

        // Assert
        $response->assertStatus(200)->assertJsonStructure(['product_category']);
    }

    public function test_show_product_supplier(): void
    {
        // Arrange
        Category::factory()->create();
        Supplier::factory()->create();
        $product = Product::factory()->create();

        // Act
        $response = $this->get(route('product_supplier', $product->id));

        // Assert
        $response->assertStatus(200)->assertJsonStructure(['product_supplier']);
    }

    public function test_store_product(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();
        
        // Act
        $response = $this->post(route('store_products'), [
            'name' => 'Test Product',
            'sku_code' => 'TP123',
            'description' => 'Test Description',
            'price' => 100.00,
            'cost_price' => 50.00,
            'status' => true,
            'additional_features' => json_encode(['feature1' => 'value1', 'feature2' => 'value2']),
            'category_id' => $category->id,
            'supplier_id' => $supplier->id
        ]);

        // Assert
        $response->assertStatus(201)->assertJsonStructure(['products']);
    }

    public function test_update_product(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();

        // Act
        $response = $this->put(route('update_product', $product->id), [
            'name' => 'Updated Product Name',
            'sku_code' => 'TP123',
            'description' => 'Test Description',
            'price' => 100.00,
            'cost_price' => 50.00,
            'status' => true,
            'additional_features' => json_encode(['feature1' => 'value1', 'feature2' => 'value2']),
            'category_id' => $category->id,
            'supplier_id' => $supplier->id
        ]);

        // Assert
        $response->assertStatus(200)->assertJsonStructure(['product']);
    }

    public function test_partial_update_product(): void
    {
        // Arrange
        Category::factory()->create();
        Supplier::factory()->create();
        $product = Product::factory()->create();

        // Act
        $response = $this->patch(route('partial_update_product', $product->id), [
            'name' => 'Updated Partial Product Name'
        ]);

        // Assert
        $response->assertStatus(200)->assertJsonStructure(['product']);
    }

    public function test_delete_product(): void
    {
        // Arrange
        Category::factory()->create();
        Supplier::factory()->create();
        $product = Product::factory()->create();

        // Act
        $response = $this->delete(route('delete_products', $product->id));

        // Assert
        $response->assertStatus(200)->assertJsonStructure(['message']);
    }
    
}

