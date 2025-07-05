<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\SuperAdmin\Company;
use App\Models\User;
use App\Services\TenantHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $tenantHelper;
    protected $company1;
    protected $company2;
    protected $user1;
    protected $user2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tenantHelper = app(TenantHelper::class);
        
        // Create test companies
        $this->company1 = Company::factory()->create(['name' => 'Company One']);
        $this->company2 = Company::factory()->create(['name' => 'Company Two']);
        
        // Create test users
        $this->user1 = User::factory()->create(['company_id' => $this->company1->id]);
        $this->user2 = User::factory()->create(['company_id' => $this->company2->id]);
    }

    /** @test */
    public function products_are_isolated_by_tenant()
    {
        // Create categories for each company
        $category1 = $this->tenantHelper->withTenant($this->company1, function () {
            return Category::factory()->create(['name' => 'Category 1']);
        });

        $category2 = $this->tenantHelper->withTenant($this->company2, function () {
            return Category::factory()->create(['name' => 'Category 2']);
        });

        // Create products for each company
        $product1 = $this->tenantHelper->withTenant($this->company1, function () use ($category1) {
            return Product::factory()->create([
                'name' => 'Product 1',
                'category_id' => $category1->id
            ]);
        });

        $product2 = $this->tenantHelper->withTenant($this->company2, function () use ($category2) {
            return Product::factory()->create([
                'name' => 'Product 2',
                'category_id' => $category2->id
            ]);
        });

        // Test isolation: Company 1 should only see its products
        $this->tenantHelper->withTenant($this->company1, function () use ($product1) {
            $this->assertEquals(1, Product::count());
            $this->assertEquals($product1->id, Product::first()->id);
        });

        // Test isolation: Company 2 should only see its products
        $this->tenantHelper->withTenant($this->company2, function () use ($product2) {
            $this->assertEquals(1, Product::count());
            $this->assertEquals($product2->id, Product::first()->id);
        });

        // Test without tenant context: should see all products
        $this->tenantHelper->withoutTenant(function () {
            $this->assertEquals(2, Product::withoutTenantScope()->count());
        });
    }

    /** @test */
    public function categories_are_isolated_by_tenant()
    {
        // Create categories for each company
        $category1 = $this->tenantHelper->withTenant($this->company1, function () {
            return Category::factory()->create(['name' => 'Electronics']);
        });

        $category2 = $this->tenantHelper->withTenant($this->company2, function () {
            return Category::factory()->create(['name' => 'Electronics']);
        });

        // Test isolation
        $this->tenantHelper->withTenant($this->company1, function () use ($category1) {
            $this->assertEquals(1, Category::count());
            $this->assertEquals($category1->id, Category::first()->id);
        });

        $this->tenantHelper->withTenant($this->company2, function () use ($category2) {
            $this->assertEquals(1, Category::count());
            $this->assertEquals($category2->id, Category::first()->id);
        });
    }

    /** @test */
    public function tenant_aware_validation_works()
    {
        // Create a category in company 1
        $category1 = $this->tenantHelper->withTenant($this->company1, function () {
            return Category::factory()->create(['name' => 'Electronics']);
        });

        // Try to create a product in company 2 with category from company 1
        $this->tenantHelper->withTenant($this->company2, function () use ($category1) {
            // This should fail because category belongs to different tenant
            $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
            
            Product::factory()->create([
                'category_id' => $category1->id
            ]);
        });
    }

    /** @test */
    public function app_settings_are_isolated_by_tenant()
    {
        // Create settings for each company
        $this->tenantHelper->withTenant($this->company1, function () {
            \App\Models\AppSetting::set('site_name', 'Company One Site');
            \App\Models\AppSetting::set('currency', 'USD');
        });

        $this->tenantHelper->withTenant($this->company2, function () {
            \App\Models\AppSetting::set('site_name', 'Company Two Site');
            \App\Models\AppSetting::set('currency', 'EUR');
        });

        // Test isolation
        $this->tenantHelper->withTenant($this->company1, function () {
            $this->assertEquals('Company One Site', \App\Models\AppSetting::get('site_name'));
            $this->assertEquals('USD', \App\Models\AppSetting::get('currency'));
        });

        $this->tenantHelper->withTenant($this->company2, function () {
            $this->assertEquals('Company Two Site', \App\Models\AppSetting::get('site_name'));
            $this->assertEquals('EUR', \App\Models\AppSetting::get('currency'));
        });
    }

    /** @test */
    public function super_admin_can_access_all_tenant_data()
    {
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
            'company_id' => null
        ]);

        // Create data for both companies
        $this->tenantHelper->withTenant($this->company1, function () {
            Category::factory()->create();
            Product::factory()->create();
        });

        $this->tenantHelper->withTenant($this->company2, function () {
            Category::factory()->create();
            Product::factory()->create();
        });

        // Super admin should see all data without tenant scope
        $this->actingAs($superAdmin);
        
        $allProducts = Product::withoutTenantScope()->count();
        $allCategories = Category::withoutTenantScope()->count();
        
        $this->assertEquals(2, $allProducts);
        $this->assertEquals(2, $allCategories);
    }

    /** @test */
    public function route_model_binding_respects_tenant_scope()
    {
        // Create products for each company
        $product1 = $this->tenantHelper->withTenant($this->company1, function () {
            return Product::factory()->create();
        });

        $product2 = $this->tenantHelper->withTenant($this->company2, function () {
            return Product::factory()->create();
        });

        // User from company 1 accessing their product should work
        $this->actingAs($this->user1);
        $this->tenantHelper->setContext($this->company1);
        
        $response = $this->get(route('admin.products.show', $product1));
        $response->assertStatus(200);

        // User from company 1 trying to access company 2's product should fail
        $response = $this->get(route('admin.products.show', $product2));
        $response->assertStatus(404);
    }

    /** @test */
    public function bulk_operations_respect_tenant_scope()
    {
        // Create products for both companies
        $products1 = $this->tenantHelper->withTenant($this->company1, function () {
            return Product::factory()->count(3)->create();
        });

        $products2 = $this->tenantHelper->withTenant($this->company2, function () {
            return Product::factory()->count(2)->create();
        });

        // User from company 1 should only be able to bulk edit their products
        $this->actingAs($this->user1);
        $this->tenantHelper->setContext($this->company1);

        $response = $this->post(route('admin.products.bulk-action'), [
            'action' => 'activate',
            'products' => $products1->pluck('id')->toArray()
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify only company 1's products were affected
        $this->tenantHelper->withTenant($this->company1, function () {
            $this->assertEquals(3, Product::where('is_active', true)->count());
        });

        $this->tenantHelper->withTenant($this->company2, function () {
            $this->assertEquals(0, Product::where('is_active', true)->count());
        });
    }

    /** @test */
    public function tenant_helper_methods_work_correctly()
    {
        // Test current tenant detection
        $this->tenantHelper->setContext($this->company1);
        $this->assertEquals($this->company1->id, $this->tenantHelper->currentId());

        // Test tenant switching
        $this->tenantHelper->setContext($this->company2);
        $this->assertEquals($this->company2->id, $this->tenantHelper->currentId());

        // Test clearing context
        $this->tenantHelper->clearContext();
        $this->assertNull($this->tenantHelper->currentId());
    }

    /** @test */
    public function tenant_aware_unique_validation_works()
    {
        // Create a category in company 1
        $this->tenantHelper->withTenant($this->company1, function () {
            Category::factory()->create(['name' => 'Electronics']);
        });

        // Should be able to create category with same name in company 2
        $this->tenantHelper->withTenant($this->company2, function () {
            $category = Category::factory()->create(['name' => 'Electronics']);
            $this->assertNotNull($category);
        });

        // Should not be able to create duplicate in same company
        $this->tenantHelper->withTenant($this->company1, function () {
            $this->expectException(\Illuminate\Database\QueryException::class);
            Category::factory()->create(['name' => 'Electronics']);
        });
    }
}
