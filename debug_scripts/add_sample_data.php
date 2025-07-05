<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ADDING SAMPLE DATA FOR CUSTOM DOMAIN COMPANIES ===\n\n";

try {
    $companies = [
        'greenvalleyherbs.local' => 'Green Valley Herbs',
        'organicnature.local' => 'Organic Nature Store',
        'herbalwellness.local' => 'Herbal Wellness Co'
    ];

    foreach ($companies as $domain => $companyName) {
        $company = \App\Models\SuperAdmin\Company::where('domain', $domain)->first();
        
        if (!$company) {
            echo "❌ Company not found: {$domain}\n";
            continue;
        }
        
        echo "🏢 Adding data for: {$companyName}\n";
        
        // Create categories for this company
        $categories = [
            ['name' => 'Herbal Teas', 'slug' => 'herbal-teas', 'description' => 'Natural organic teas for wellness'],
            ['name' => 'Skincare', 'slug' => 'skincare', 'description' => 'Natural skincare products'],
            ['name' => 'Supplements', 'slug' => 'supplements', 'description' => 'Herbal health supplements'],
            ['name' => 'Aromatherapy', 'slug' => 'aromatherapy', 'description' => 'Essential oils and aromatherapy']
        ];
        
        foreach ($categories as $categoryData) {
            $category = \App\Models\Category::updateOrCreate(
                ['company_id' => $company->id, 'slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'status' => 'active',
                    'sort_order' => 1,
                    'parent_id' => null
                ]
            );
            echo "   ✅ Category: {$category->name}\n";
        }
        
        // Create products for this company
        $products = [
            [
                'name' => 'Organic Green Tea',
                'slug' => 'organic-green-tea',
                'description' => 'Premium organic green tea with antioxidants',
                'short_description' => 'Premium organic green tea',
                'price' => 299.99,
                'category' => 'herbal-teas'
            ],
            [
                'name' => 'Natural Face Cream',
                'slug' => 'natural-face-cream',
                'description' => 'Moisturizing face cream with natural ingredients',
                'short_description' => 'Natural moisturizing cream',
                'price' => 599.99,
                'category' => 'skincare'
            ],
            [
                'name' => 'Turmeric Supplement',
                'slug' => 'turmeric-supplement',
                'description' => 'Organic turmeric capsules for inflammation',
                'short_description' => 'Organic turmeric capsules',
                'price' => 899.99,
                'category' => 'supplements'
            ],
            [
                'name' => 'Lavender Essential Oil',
                'slug' => 'lavender-essential-oil',
                'description' => 'Pure lavender oil for relaxation',
                'short_description' => 'Pure lavender essential oil',
                'price' => 1299.99,
                'category' => 'aromatherapy'
            ]
        ];
        
        foreach ($products as $productData) {
            $category = \App\Models\Category::where('company_id', $company->id)
                                           ->where('slug', $productData['category'])
                                           ->first();
            
            if ($category) {
                $product = \App\Models\Product::updateOrCreate(
                    ['company_id' => $company->id, 'slug' => $productData['slug']],
                    [
                        'name' => $productData['name'],
                        'description' => $productData['description'],
                        'short_description' => $productData['short_description'],
                        'price' => $productData['price'],
                        'category_id' => $category->id,
                        'sku' => strtoupper(str_replace('-', '', $productData['slug'])),
                        'stock_quantity' => 50,
                        'min_stock_level' => 5,
                        'status' => 'active',
                        'is_featured' => true,
                        'sort_order' => 1
                    ]
                );
                echo "   ✅ Product: {$product->name}\n";
            }
        }
        
        // Create a banner for this company
        $banner = \App\Models\Banner::updateOrCreate(
            ['company_id' => $company->id, 'title' => 'Welcome to ' . $companyName],
            [
                'title' => 'Welcome to ' . $companyName,
                'description' => 'Discover our natural and organic products',
                'position' => 'top',
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
                'sort_order' => 1,
                'link_url' => '/shop'
            ]
        );
        echo "   ✅ Banner: {$banner->title}\n";
        
        echo "   📊 Summary for {$companyName}:\n";
        echo "      Categories: " . \App\Models\Category::where('company_id', $company->id)->count() . "\n";
        echo "      Products: " . \App\Models\Product::where('company_id', $company->id)->count() . "\n";
        echo "      Banners: " . \App\Models\Banner::where('company_id', $company->id)->count() . "\n\n";
    }
    
    echo "🎉 SAMPLE DATA ADDED SUCCESSFULLY!\n\n";
    
    echo "🔧 NOW TEST THESE URLS:\n";
    echo "Store: http://greenvalleyherbs.local:8000/shop\n";
    echo "Store: http://organicnature.local:8000/shop\n";
    echo "Store: http://herbalwellness.local:8000/shop\n\n";
    
    echo "💡 NAVIGATION SHOULD NOW WORK:\n";
    echo "✅ Home/logo links will stay on the same domain\n";
    echo "✅ Categories dropdown will show products\n";
    echo "✅ Products will be visible\n";
    echo "✅ No more redirects to SaaS landing page\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
