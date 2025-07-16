<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\products;
use App\Models\brands;
use App\Models\categories;

class ProductSearchLevenshteinTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_similar_products_when_typo_in_search()
    {
        // Arrange: Buat data produk dan kategori, brand
        $category = categories::create(['name' => 'Makanan']);
        $brand = brands::create(['name' => 'Brand A']);

        products::create([
            'name' => 'Aqua Botol',
            'description' => 'Air mineral kemasan',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 5000,
            'quantity' => 5,
        ]);

        // Act: Kirim request dengan keyword typo
        $response = $this->get('/search?q=pondok mawer');

        // Assert: Produk tetap ditemukan
        $response->assertStatus(200);
        $response->assertSee('Pondok Mawar');
    }

    /** @test */
    public function it_does_not_return_product_if_not_similar()
    {
        $category = categories::create(['name' => 'Minuman']);
        $brand = brands::create(['name' => 'Brand B']);

        products::create([
            'name' => 'Plastik Kue',
            'description' => 'Kemasan Untuk Kue',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 5000,
            'quantity' => 5,
        ]);

        $response = $this->get('/search?q=sabun mandi');

        $response->assertStatus(200);
        $response->assertDontSee('sabun mandi');
    }
}
