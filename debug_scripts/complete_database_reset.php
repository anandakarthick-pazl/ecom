<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== COMPLETE DATABASE RESET AND SAMPLE DATA INSERTION ===\n\n";

try {
    echo "🧹 STEP 1: CLEARING ALL DATA...\n";
    
    // Clear all tenant-related data (preserves structure)
    $tables = [
        'cart_items',
        'order_items', 
        'orders',
        'pos_sale_items',
        'pos_sales',
        'grn_items',
        'goods_receipt_notes',
        'purchase_order_items',
        'purchase_orders',
        'estimate_items',
        'estimates',
        'stock_adjustment_items',
        'stock_adjustments',
        'products',
        'categories',
        'banners',
        'offers',
        'suppliers',
        'customers',
        'notifications',
        'app_settings',
        'users' // Will recreate admin users
    ];
    
    foreach ($tables as $table) {
        \Illuminate\Support\Facades\DB::table($table)->delete();
        echo "   ✅ Cleared: {$table}\n";
    }
    
    // Clear companies but keep themes and packages
    \Illuminate\Support\Facades\DB::table('companies')->delete();
    echo "   ✅ Cleared: companies\n";
    
    echo "\n🎨 STEP 2: ENSURING THEMES AND PACKAGES EXIST...\n";
    
    // Create default theme
    $defaultTheme = \App\Models\SuperAdmin\Theme::firstOrCreate(
        ['slug' => 'default'],
        [
            'name' => 'Modern Herbal Theme',
            'description' => 'Clean, modern theme for herbal stores',
            'category' => 'ecommerce',
            'status' => 'active',
            'config' => json_encode([
                'primary_color' => '#2d5016',
                'secondary_color' => '#6b8e23',
                'accent_color' => '#8fbc8f',
                'font_family' => 'Inter, sans-serif'
            ])
        ]
    );
    echo "   ✅ Theme: {$defaultTheme->name}\n";
    
    // Create default package
    $defaultPackage = \App\Models\SuperAdmin\Package::firstOrCreate(
        ['slug' => 'premium'],
        [
            'name' => 'Premium Plan',
            'description' => 'Full-featured ecommerce plan',
            'price' => 99.99,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'features' => json_encode([
                'products' => 'unlimited',
                'storage' => '50GB',
                'support' => '24/7',
                'analytics' => true,
                'multi_store' => true
            ])
        ]
    );
    echo "   ✅ Package: {$defaultPackage->name}\n";
    
    echo "\n🏢 STEP 3: CREATING 3 COMPANIES...\n";
    
    $companiesData = [
        [
            'name' => 'Green Valley Herbs',
            'slug' => 'green-valley-herbs',
            'email' => 'admin@greenvalleyherbs.com',
            'domain' => 'greenvalleyherbs.local',
            'phone' => '+91 98765 43210',
            'address' => '123 Herb Garden Street, Mumbai, Maharashtra 400001',
            'description' => 'Premium organic herbs and natural wellness products'
        ],
        [
            'name' => 'Organic Nature Store',
            'slug' => 'organic-nature-store',
            'email' => 'admin@organicnature.com',
            'domain' => 'organicnature.local',
            'phone' => '+91 87654 32109',
            'address' => '456 Nature Boulevard, Bangalore, Karnataka 560001',
            'description' => 'Your trusted source for organic and natural products'
        ],
        [
            'name' => 'Herbal Wellness Co',
            'slug' => 'herbal-wellness-co',
            'email' => 'admin@herbalwellness.com',
            'domain' => 'herbalwellness.local',
            'phone' => '+91 76543 21098',
            'address' => '789 Wellness Avenue, Pune, Maharashtra 411001',
            'description' => 'Traditional herbal remedies for modern wellness'
        ]
    ];
    
    foreach ($companiesData as $index => $companyData) {
        // Create company
        $company = \App\Models\SuperAdmin\Company::create([
            'name' => $companyData['name'],
            'slug' => $companyData['slug'],
            'email' => $companyData['email'],
            'domain' => $companyData['domain'],
            'phone' => $companyData['phone'],
            'address' => $companyData['address'],
            'status' => 'active',
            'trial_ends_at' => now()->addDays(30),
            'subscription_ends_at' => now()->addYear(),
            'theme_id' => $defaultTheme->id,
            'package_id' => $defaultPackage->id,
            'created_by' => 1
        ]);
        
        echo "   ✅ Company: {$company->name} (ID: {$company->id})\n";
        
        // Create admin user
        $adminUser = \App\Models\User::create([
            'name' => $companyData['name'] . ' Admin',
            'email' => $companyData['email'],
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'admin',
            'is_super_admin' => false,
            'status' => 'active',
            'company_id' => $company->id,
            'email_verified_at' => now()
        ]);
        echo "      👤 Admin: {$adminUser->email}\n";
        
        // Create categories
        $categories = [
            ['name' => 'Herbal Teas', 'slug' => 'herbal-teas', 'description' => 'Natural organic teas for wellness and relaxation'],
            ['name' => 'Skincare', 'slug' => 'skincare', 'description' => 'Natural skincare products for healthy glowing skin'],
            ['name' => 'Supplements', 'slug' => 'supplements', 'description' => 'Herbal health supplements and vitamins'],
            ['name' => 'Aromatherapy', 'slug' => 'aromatherapy', 'description' => 'Essential oils and aromatherapy products'],
            ['name' => 'Ayurvedic', 'slug' => 'ayurvedic', 'description' => 'Traditional Ayurvedic medicines and herbs']
        ];
        
        $createdCategories = [];
        foreach ($categories as $catData) {
            $category = \App\Models\Category::create([
                'name' => $catData['name'],
                'slug' => $catData['slug'],
                'description' => $catData['description'],
                'status' => 'active',
                'sort_order' => 1,
                'parent_id' => null,
                'company_id' => $company->id
            ]);
            $createdCategories[] = $category;
            echo "      📂 Category: {$category->name}\n";
        }
        
        // Create products
        $productsData = [
            // Herbal Teas
            ['name' => 'Green Tea Premium', 'category' => 0, 'price' => 299.99, 'stock' => 50, 'description' => 'Premium organic green tea with antioxidants'],
            ['name' => 'Chamomile Tea', 'category' => 0, 'price' => 249.99, 'stock' => 40, 'description' => 'Soothing chamomile tea for better sleep'],
            ['name' => 'Ginger Lemon Tea', 'category' => 0, 'price' => 199.99, 'stock' => 60, 'description' => 'Refreshing ginger lemon tea for digestion'],
            
            // Skincare
            ['name' => 'Aloe Vera Gel', 'category' => 1, 'price' => 399.99, 'stock' => 30, 'description' => 'Pure aloe vera gel for skin hydration'],
            ['name' => 'Turmeric Face Mask', 'category' => 1, 'price' => 599.99, 'stock' => 25, 'description' => 'Natural turmeric face mask for glowing skin'],
            ['name' => 'Rose Water Toner', 'category' => 1, 'price' => 299.99, 'stock' => 35, 'description' => 'Pure rose water toner for all skin types'],
            
            // Supplements
            ['name' => 'Ashwagandha Capsules', 'category' => 2, 'price' => 899.99, 'stock' => 20, 'description' => 'Organic ashwagandha for stress relief'],
            ['name' => 'Moringa Powder', 'category' => 2, 'price' => 449.99, 'stock' => 45, 'description' => 'Nutrient-rich moringa leaf powder'],
            ['name' => 'Spirulina Tablets', 'category' => 2, 'price' => 699.99, 'stock' => 30, 'description' => 'High-protein spirulina tablets'],
            
            // Aromatherapy
            ['name' => 'Lavender Essential Oil', 'category' => 3, 'price' => 1299.99, 'stock' => 15, 'description' => 'Pure lavender oil for relaxation'],
            ['name' => 'Tea Tree Oil', 'category' => 3, 'price' => 999.99, 'stock' => 20, 'description' => 'Antibacterial tea tree essential oil'],
            ['name' => 'Eucalyptus Oil', 'category' => 3, 'price' => 799.99, 'stock' => 25, 'description' => 'Refreshing eucalyptus essential oil'],
            
            // Ayurvedic
            ['name' => 'Triphala Powder', 'category' => 4, 'price' => 349.99, 'stock' => 40, 'description' => 'Traditional triphala for digestive health'],
            ['name' => 'Neem Capsules', 'category' => 4, 'price' => 599.99, 'stock' => 35, 'description' => 'Pure neem extract capsules for detox'],
            ['name' => 'Brahmi Oil', 'category' => 4, 'price' => 799.99, 'stock' => 20, 'description' => 'Brahmi hair oil for mental clarity']
        ];
        
        foreach ($productsData as $prodIndex => $prodData) {
            $product = \App\Models\Product::create([
                'name' => $prodData['name'],
                'slug' => \Illuminate\Support\Str::slug($prodData['name']),
                'description' => $prodData['description'],
                'short_description' => substr($prodData['description'], 0, 100),
                'price' => $prodData['price'],
                'category_id' => $createdCategories[$prodData['category']]->id,
                'sku' => 'PRD' . str_pad($company->id, 2, '0', STR_PAD_LEFT) . str_pad($prodIndex + 1, 3, '0', STR_PAD_LEFT),
                'stock_quantity' => $prodData['stock'],
                'min_stock_level' => 5,
                'status' => 'active',
                'is_featured' => $prodIndex < 6, // First 6 products are featured
                'sort_order' => 1,
                'company_id' => $company->id,
                'weight' => rand(50, 500),
                'weight_unit' => 'g',
                'meta_title' => $prodData['name'] . ' - ' . $company->name,
                'meta_description' => $prodData['description']
            ]);
            echo "      🛍️ Product: {$product->name} (₹{$product->price}, Stock: {$product->stock_quantity})\n";
        }
        
        // Create suppliers
        $suppliers = [
            ['name' => 'Organic Farms Ltd', 'contact' => 'Mr. Sharma', 'phone' => '9876543210'],
            ['name' => 'Herbal Traders Co', 'contact' => 'Ms. Patel', 'phone' => '8765432109'],
            ['name' => 'Natural Products Inc', 'contact' => 'Mr. Kumar', 'phone' => '7654321098']
        ];
        
        foreach ($suppliers as $suppData) {
            $supplier = \App\Models\Supplier::create([
                'name' => $suppData['name'],
                'contact_person' => $suppData['contact'],
                'phone' => $suppData['phone'],
                'email' => strtolower(str_replace(' ', '', $suppData['name'])) . '@suppliers.com',
                'address' => 'Supplier Address, City, State',
                'status' => 'active',
                'company_id' => $company->id
            ]);
            echo "      🏭 Supplier: {$supplier->name}\n";
        }
        
        // Create customers
        $customers = [
            ['name' => 'Rajesh Kumar', 'mobile' => '9876543210', 'email' => 'rajesh@example.com'],
            ['name' => 'Priya Sharma', 'mobile' => '8765432109', 'email' => 'priya@example.com'],
            ['name' => 'Amit Patel', 'mobile' => '7654321098', 'email' => 'amit@example.com'],
            ['name' => 'Sunita Singh', 'mobile' => '6543210987', 'email' => 'sunita@example.com'],
            ['name' => 'Vikram Gupta', 'mobile' => '5432109876', 'email' => 'vikram@example.com']
        ];
        
        foreach ($customers as $custData) {
            $customer = \App\Models\Customer::create([
                'name' => $custData['name'],
                'mobile' => $custData['mobile'],
                'email' => $custData['email'],
                'address' => 'Customer Address, City, State, PIN',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400001',
                'status' => 'active',
                'company_id' => $company->id
            ]);
            echo "      👥 Customer: {$customer->name}\n";
        }
        
        // Create banners
        $banner = \App\Models\Banner::create([
            'title' => 'Welcome to ' . $company->name,
            'description' => 'Discover our premium collection of natural and organic products',
            'position' => 'top',
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'sort_order' => 1,
            'link_url' => '/shop',
            'company_id' => $company->id
        ]);
        echo "      🎨 Banner: {$banner->title}\n";
        
        // Create app settings
        $settings = [
            'company_name' => $company->name,
            'company_email' => $company->email,
            'company_phone' => $company->phone,
            'company_address' => $company->address,
            'currency' => 'INR',
            'currency_symbol' => '₹',
            'tax_rate' => '18.0',
            'shipping_charge' => '50.0',
            'free_shipping_limit' => '500.0',
            'low_stock_threshold' => '10'
        ];
        
        foreach ($settings as $key => $value) {
            \App\Models\AppSetting::create([
                'key' => $key,
                'value' => $value,
                'company_id' => $company->id
            ]);
        }
        echo "      ⚙️ Settings: Configured\n";
        
        echo "   📊 Summary for {$company->name}:\n";
        echo "      Categories: " . \App\Models\Category::where('company_id', $company->id)->count() . "\n";
        echo "      Products: " . \App\Models\Product::where('company_id', $company->id)->count() . "\n";
        echo "      Suppliers: " . \App\Models\Supplier::where('company_id', $company->id)->count() . "\n";
        echo "      Customers: " . \App\Models\Customer::where('company_id', $company->id)->count() . "\n\n";
    }
    
    echo "🎉 DATABASE RESET AND SAMPLE DATA CREATION COMPLETE!\n\n";
    
    echo "🔐 LOGIN CREDENTIALS:\n";
    foreach ($companiesData as $companyData) {
        echo "Company: {$companyData['name']}\n";
        echo "  Admin URL: http://{$companyData['domain']}:8000/admin/login\n";
        echo "  Store URL: http://{$companyData['domain']}:8000/shop\n";
        echo "  Email: {$companyData['email']}\n";
        echo "  Password: password123\n\n";
    }
    
    echo "🌐 HOSTS FILE ENTRIES NEEDED:\n";
    echo "Add these to C:\\Windows\\System32\\drivers\\etc\\hosts:\n\n";
    foreach ($companiesData as $companyData) {
        echo "127.0.0.1 {$companyData['domain']}\n";
    }
    
    echo "\n✅ ALL DONE! Your multi-tenant ecommerce system is ready with sample data.\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
