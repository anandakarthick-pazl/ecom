<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\SuperAdmin\Company;
use App\Models\SuperAdmin\Theme;
use App\Models\SuperAdmin\Package;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Banner;
use App\Models\Offer;
use App\Models\Supplier;
use App\Models\Cart;

class CompleteSampleDataSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info('Creating Super Admin Users...');
        $this->createSuperAdminUsers();

        $this->command->info('Creating Companies with Sample Data...');
        $this->createCompaniesWithSampleData();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Sample data creation completed successfully!');
    }

    private function createSuperAdminUsers()
    {
        $superAdmins = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@herbal-ecom.com',
                'password' => Hash::make('SuperAdmin@123'),
                'is_super_admin' => true,
                'role' => 'admin',
                'status' => 'active',
                'phone' => '+1-555-0001',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Platform Manager',
                'email' => 'manager@herbal-ecom.com',
                'password' => Hash::make('Manager@123'),
                'is_super_admin' => true,
                'role' => 'manager',
                'status' => 'active',
                'phone' => '+1-555-0002',
                'email_verified_at' => now()
            ],
            [
                'name' => 'System Admin',
                'email' => 'admin@herbal-ecom.com',
                'password' => Hash::make('SystemAdmin@123'),
                'is_super_admin' => true,
                'role' => 'admin',
                'status' => 'active',
                'phone' => '+1-555-0003',
                'email_verified_at' => now()
            ]
        ];

        foreach ($superAdmins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }
    }

    private function createCompaniesWithSampleData()
    {
        $superAdmin = User::where('is_super_admin', true)->first();
        
        // Ensure we have themes and packages
        $this->ensureThemesAndPackages();
        
        $companies = [
            [
                'name' => 'Herbal Wellness Solutions',
                'slug' => 'herbal-wellness',
                'domain' => 'herbal-wellness.local',
                'email' => 'contact@herbal-wellness.com',
                'phone' => '+1-555-1001',
                'address' => '123 Wellness Street',
                'city' => 'Los Angeles',
                'state' => 'California',
                'country' => 'USA',
                'postal_code' => '90210',
                'business_type' => 'herbal_supplements'
            ],
            [
                'name' => 'Natural Health Store',
                'slug' => 'natural-health',
                'domain' => 'natural-health.local',
                'email' => 'info@natural-health.com',
                'phone' => '+1-555-1002',
                'address' => '456 Nature Avenue',
                'city' => 'Denver',
                'state' => 'Colorado',
                'country' => 'USA',
                'postal_code' => '80202',
                'business_type' => 'organic_products'
            ],
            [
                'name' => 'Ayurvedic Herbs Co',
                'slug' => 'ayurvedic-herbs',
                'domain' => 'ayurvedic-herbs.local',
                'email' => 'support@ayurvedic-herbs.com',
                'phone' => '+1-555-1003',
                'address' => '789 Ayurveda Lane',
                'city' => 'Austin',
                'state' => 'Texas',
                'country' => 'USA',
                'postal_code' => '73301',
                'business_type' => 'ayurvedic_medicine'
            ],
            [
                'name' => 'Green Leaf Pharmacy',
                'slug' => 'green-leaf',
                'domain' => 'green-leaf.local',
                'email' => 'orders@green-leaf.com',
                'phone' => '+1-555-1004',
                'address' => '321 Green Street',
                'city' => 'Portland',
                'state' => 'Oregon',
                'country' => 'USA',
                'postal_code' => '97201',
                'business_type' => 'herbal_pharmacy'
            ],
            [
                'name' => 'Traditional Medicine Hub',
                'slug' => 'traditional-medicine',
                'domain' => 'traditional-medicine.local',
                'email' => 'hello@traditional-medicine.com',
                'phone' => '+1-555-1005',
                'address' => '654 Traditional Way',
                'city' => 'Seattle',
                'state' => 'Washington',
                'country' => 'USA',
                'postal_code' => '98101',
                'business_type' => 'traditional_medicine'
            ]
        ];

        foreach ($companies as $companyData) {
            $company = $this->createCompany($companyData, $superAdmin);
            $this->createSampleDataForCompany($company, $companyData);
        }
    }

    private function ensureThemesAndPackages()
    {
        // Create default theme if not exists
        $defaultTheme = Theme::firstOrCreate(
            ['name' => 'Default Theme'],
            [
                'slug' => 'default',
                'description' => 'Default theme for herbal e-commerce',
                'version' => '1.0.0',
                'status' => 'active',
                'settings' => [
                    'primary_color' => '#10b981',
                    'secondary_color' => '#059669',
                    'layout' => 'modern'
                ]
            ]
        );

        // Create packages if not exist
        $basicPackage = Package::firstOrCreate(
            ['name' => 'Basic Plan'],
            [
                'slug' => 'basic',
                'description' => 'Basic package for small businesses',
                'price' => 29.99,
                'features' => [
                    'max_products' => 100,
                    'max_orders' => 500,
                    'storage' => '1GB',
                    'support' => 'email'
                ],
                'status' => 'active'
            ]
        );

        Package::firstOrCreate(
            ['name' => 'Professional Plan'],
            [
                'slug' => 'professional',
                'description' => 'Professional package for growing businesses',
                'price' => 59.99,
                'features' => [
                    'max_products' => 500,
                    'max_orders' => 2000,
                    'storage' => '5GB',
                    'support' => 'priority'
                ],
                'status' => 'active'
            ]
        );

        Package::firstOrCreate(
            ['name' => 'Enterprise Plan'],
            [
                'slug' => 'enterprise',
                'description' => 'Enterprise package for large businesses',
                'price' => 99.99,
                'features' => [
                    'max_products' => -1,
                    'max_orders' => -1,
                    'storage' => '50GB',
                    'support' => '24/7'
                ],
                'status' => 'active'
            ]
        );
    }

    private function createCompany($companyData, $superAdmin)
    {
        $businessType = $companyData['business_type'];
        unset($companyData['business_type']);

        $theme = Theme::where('status', 'active')->first();
        $package = Package::where('status', 'active')->first();

        $company = Company::updateOrCreate(
            ['slug' => $companyData['slug']],
            array_merge($companyData, [
                'theme_id' => $theme->id,
                'package_id' => $package->id,
                'status' => 'active',
                'trial_ends_at' => now()->addDays(30),
                'subscription_ends_at' => now()->addYear(),
                'settings' => [
                    'business_type' => $businessType,
                    'currency' => 'USD',
                    'timezone' => 'America/New_York',
                    'tax_rate' => 8.25
                ],
                'created_by' => $superAdmin->id
            ])
        );

        // Create company admin user
        $this->createCompanyAdmin($company);

        return $company;
    }

    private function createCompanyAdmin($company)
    {
        $adminEmail = 'admin@' . str_replace(['http://', 'https://', '.local'], '', $company->domain) . '.com';
        
        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $company->name . ' Administrator',
                'email' => $adminEmail,
                'password' => Hash::make('Admin@123'),
                'company_id' => $company->id,
                'role' => 'admin',
                'is_super_admin' => false,
                'status' => 'active',
                'phone' => $company->phone,
                'email_verified_at' => now()
            ]
        );

        // Create additional staff users
        $staffRoles = ['manager', 'staff'];
        foreach ($staffRoles as $index => $role) {
            $staffEmail = $role . '@' . str_replace(['http://', 'https://', '.local'], '', $company->domain) . '.com';
            
            User::updateOrCreate(
                ['email' => $staffEmail],
                [
                    'name' => $company->name . ' ' . ucfirst($role),
                    'email' => $staffEmail,
                    'password' => Hash::make(ucfirst($role) . '@123'),
                    'company_id' => $company->id,
                    'role' => $role,
                    'is_super_admin' => false,
                    'status' => 'active',
                    'email_verified_at' => now()
                ]
            );
        }
    }

    private function createSampleDataForCompany($company, $companyData)
    {
        $businessType = $companyData['business_type'] ?? 'herbal_supplements';
        
        // Create categories based on business type
        $categories = $this->createCategoriesForCompany($company, $businessType);
        
        // Create products
        $products = $this->createProductsForCompany($company, $categories, $businessType);
        
        // Create suppliers
        $suppliers = $this->createSuppliersForCompany($company);
        
        // Create customers
        $customers = $this->createCustomersForCompany($company);
        
        // Create orders
        $this->createOrdersForCompany($company, $products, $customers);
        
        // Create banners
        $this->createBannersForCompany($company);
        
        // Create offers
        $this->createOffersForCompany($company, $products);
    }

    private function createCategoriesForCompany($company, $businessType)
    {
        $categoryData = [
            'herbal_supplements' => [
                'Immune Support', 'Digestive Health', 'Heart Health', 'Energy & Vitality', 
                'Sleep & Relaxation', 'Joint Support', 'Brain Health', 'Weight Management'
            ],
            'organic_products' => [
                'Organic Herbs', 'Organic Spices', 'Organic Teas', 'Organic Oils',
                'Organic Superfoods', 'Organic Skincare', 'Organic Supplements'
            ],
            'ayurvedic_medicine' => [
                'Vata Products', 'Pitta Products', 'Kapha Products', 'Rasayana (Rejuvenatives)',
                'Panchakarma Medicines', 'Herbal Formulations', 'Ayurvedic Oils'
            ],
            'herbal_pharmacy' => [
                'Prescription Herbs', 'OTC Remedies', 'Herbal Extracts', 'Tinctures',
                'Capsules & Tablets', 'Powders', 'Topical Applications'
            ],
            'traditional_medicine' => [
                'Chinese Herbs', 'Herbal Formulas', 'Traditional Remedies', 'Medicinal Teas',
                'Herbal Preparations', 'Ancient Formulations'
            ]
        ];

        $categories = [];
        $categoryNames = $categoryData[$businessType] ?? $categoryData['herbal_supplements'];

        foreach ($categoryNames as $index => $name) {
            $category = Category::create([
                'company_id' => $company->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "High-quality {$name} products for your health and wellness needs.",
                'image' => 'categories/category-' . ($index + 1) . '.jpg',
                'is_active' => '1',
                'sort_order' => $index + 1
            ]);
            $categories[] = $category;
        }

        return $categories;
    }

    private function createProductsForCompany($company, $categories, $businessType)
    {
        $products = [];
        
        foreach ($categories as $category) {
            $productCount = rand(8, 15);
            
            for ($i = 1; $i <= $productCount; $i++) {
                $productName = $this->generateProductName($category->name, $businessType, $i);
                $price = $this->generatePrice($businessType);
                $cost = $price * 0.6; // 40% margin
                
                $product = Product::create([
                    'company_id' => $company->id,
                    'category_id' => $category->id,
                    'name' => $productName,
                    'slug' => Str::slug($productName . '-' . $company->id),
                    'description' => $this->generateProductDescription($productName, $businessType),
                    'short_description' => $this->generateShortDescription($productName),
                    'sku' => strtoupper(substr($company->slug, 0, 3)) . '-' . strtoupper(substr($category->slug, 0, 3)) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'price' => $price,
                    // 'sale_price' => rand(0, 1) ? $price * 0.9 : null,
                    'cost_price' => $cost,
                    'stock' => rand(50, 500),
                    'low_stock_threshold' => rand(5, 20),
                    'weight' => rand(50, 1000) / 100, // 0.5kg to 10kg
                    'dimensions' => json_encode([
                        'length' => rand(5, 30),
                        'width' => rand(5, 30),
                        'height' => rand(2, 15)
                    ]),
                    'images' => json_encode([
                        'products/product-' . $i . '-1.jpg',
                        'products/product-' . $i . '-2.jpg',
                        'products/product-' . $i . '-3.jpg'
                    ]),
                    'is_active' => 'active',
                    'is_featured' => rand(0, 1),
                    'meta_title' => $productName . ' - Premium Quality',
                    'meta_description' => "Buy {$productName} online. High-quality, natural ingredients. Fast shipping.",
                    'tags' => $this->generateTags($businessType)
                ]);
                
                $products[] = $product;
            }
        }

        return $products;
    }

    private function createSuppliersForCompany($company)
    {
        $suppliers = [];
        $supplierNames = [
            'Natural Herbs Wholesale', 'Organic Source Ltd', 'Herbal Extracts Inc',
            'Pure Nature Suppliers', 'Traditional Medicine Co', 'Green Valley Herbs'
        ];

        foreach ($supplierNames as $index => $name) {
            $supplier = Supplier::create([
                'company_id' => $company->id,
                'name' => $name,
                'email' => strtolower(str_replace(' ', '', $name)) . '@example.com',
                'phone' => '+1-555-' . str_pad(2000 + $index, 4, '0', STR_PAD_LEFT),
                'address' => (100 + $index * 50) . ' Supplier Street',
                'city' => ['New York', 'Chicago', 'Miami', 'Dallas', 'Phoenix', 'Philadelphia'][$index],
                'state' => ['NY', 'IL', 'FL', 'TX', 'AZ', 'PA'][$index],
                'country' => 'USA',
                'postal_code' => str_pad(10000 + $index * 100, 5, '0', STR_PAD_LEFT),
                'contact_person' => ['John Smith', 'Sarah Johnson', 'Mike Davis', 'Lisa Brown', 'Tom Wilson', 'Amy Taylor'][$index],
                'website' => 'https://www.' . strtolower(str_replace(' ', '', $name)) . '.com',
                'notes' => 'Reliable supplier with quality products and timely deliveries.',
                'status' => 'active'
            ]);
            $suppliers[] = $supplier;
        }

        return $suppliers;
    }

    private function createCustomersForCompany($company)
    {
        $customers = [];
        $firstNames = ['Alex', 'Emma', 'James', 'Olivia', 'William', 'Sophia', 'Benjamin', 'Isabella', 'Lucas', 'Mia', 'Henry', 'Charlotte', 'Samuel', 'Amelia', 'David'];
        $lastNames = ['Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas'];

        for ($i = 0; $i < 25; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $email = strtolower($firstName . '.' . $lastName . '.' . $company->id . '@customer.com');

            $customer = Customer::create([
                'company_id' => $company->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => '+1-555-' . str_pad(rand(3000, 9999), 4, '0', STR_PAD_LEFT),
                'date_of_birth' => now()->subYears(rand(25, 65))->format('Y-m-d'),
                'gender' => ['male', 'female'][rand(0, 1)],
                'address' => (rand(100, 999)) . ' Customer Street',
                'city' => ['Los Angeles', 'New York', 'Chicago', 'Houston', 'Phoenix'][rand(0, 4)],
                'state' => ['CA', 'NY', 'IL', 'TX', 'AZ'][rand(0, 4)],
                'country' => 'USA',
                'postal_code' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                'notes' => 'Regular customer interested in natural health products.',
                'status' => 'active'
            ]);
            $customers[] = $customer;
        }

        return $customers;
    }

    private function createOrdersForCompany($company, $products, $customers)
    {
        for ($i = 0; $i < 15; $i++) {
            $customer = $customers[array_rand($customers)];
            $orderDate = now()->subDays(rand(1, 90));
            
            $order = Order::create([
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'order_number' => 'ORD-' . strtoupper(substr($company->slug, 0, 3)) . '-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'customer_email' => $customer->email,
                'status' => ['pending', 'processing', 'shipped', 'delivered', 'cancelled'][rand(0, 4)],
                'subtotal' => 0,
                'tax_amount' => 0,
                'shipping_amount' => rand(0, 1) ? 0 : 9.99,
                'discount_amount' => 0,
                'total_amount' => 0,
                'shipping_address' => json_encode([
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'address' => $customer->address,
                    'city' => $customer->city,
                    'state' => $customer->state,
                    'postal_code' => $customer->postal_code,
                    'country' => $customer->country
                ]),
                'billing_address' => json_encode([
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'address' => $customer->address,
                    'city' => $customer->city,
                    'state' => $customer->state,
                    'postal_code' => $customer->postal_code,
                    'country' => $customer->country
                ]),
                'payment_method' => ['credit_card', 'paypal', 'bank_transfer'][rand(0, 2)],
                'payment_status' => ['pending', 'paid', 'failed'][rand(0, 2)],
                'notes' => 'Customer order for natural health products.',
                'created_at' => $orderDate,
                'updated_at' => $orderDate
            ]);

            // Create order items
            $itemCount = rand(1, 5);
            $subtotal = 0;
            
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products[array_rand($products)];
                $quantity = rand(1, 3);
                $price = $product->sale_price ?? $product->price;
                $itemTotal = $price * $quantity;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $itemTotal
                ]);
                
                $subtotal += $itemTotal;
            }

            // Update order totals
            $taxAmount = $subtotal * 0.0825; // 8.25% tax
            $totalAmount = $subtotal + $taxAmount + $order->shipping_amount;
            
            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount
            ]);
        }
    }

    private function createBannersForCompany($company)
    {
        $banners = [
            [
                'title' => 'Welcome to ' . $company->name,
                'subtitle' => 'Discover natural wellness solutions',
                'description' => 'Shop our premium collection of herbal products and supplements.',
                'button_text' => 'Shop Now',
                'button_url' => '/products'
            ],
            [
                'title' => 'Summer Sale',
                'subtitle' => 'Up to 30% off selected items',
                'description' => 'Limited time offer on our best-selling natural products.',
                'button_text' => 'View Offers',
                'button_url' => '/offers'
            ],
            [
                'title' => 'New Arrivals',
                'subtitle' => 'Fresh herbal collections',
                'description' => 'Explore our latest additions to the product catalog.',
                'button_text' => 'Explore',
                'button_url' => '/new-arrivals'
            ]
        ];

        foreach ($banners as $index => $bannerData) {
            Banner::create([
                'company_id' => $company->id,
                'title' => $bannerData['title'],
                'subtitle' => $bannerData['subtitle'],
                'description' => $bannerData['description'],
                'image' => 'banners/banner-' . ($index + 1) . '.jpg',
                'button_text' => $bannerData['button_text'],
                'button_url' => $bannerData['button_url'],
                'status' => 'active',
                'sort_order' => $index + 1
            ]);
        }
    }

    private function createOffersForCompany($company, $products)
    {
        $offers = [
            [
                'title' => 'Buy 2 Get 1 Free',
                'description' => 'Purchase any two items and get the third one absolutely free!',
                'type' => 'percentage',
                'value' => 33.33,
                'valid_from' => now(),
                'valid_until' => now()->addDays(30)
            ],
            [
                'title' => 'First Time Customer',
                'description' => 'Get 15% off on your first order with us.',
                'type' => 'percentage',
                'value' => 15.00,
                'valid_from' => now(),
                'valid_until' => now()->addDays(60)
            ],
            [
                'title' => 'Free Shipping',
                'description' => 'Free shipping on orders over $50.',
                'type' => 'free_shipping',
                'value' => 50.00,
                'valid_from' => now(),
                'valid_until' => now()->addDays(90)
            ]
        ];

        foreach ($offers as $index => $offerData) {
            $offer = Offer::create([
                'company_id' => $company->id,
                'title' => $offerData['title'],
                'description' => $offerData['description'],
                'type' => $offerData['type'],
                'value' => $offerData['value'],
                'minimum_amount' => $offerData['type'] === 'free_shipping' ? $offerData['value'] : 0,
                'code' => strtoupper(str_replace(' ', '', $offerData['title'])) . $company->id,
                'usage_limit' => rand(50, 200),
                'used_count' => rand(0, 20),
                'valid_from' => $offerData['valid_from'],
                'valid_until' => $offerData['valid_until'],
                'status' => 'active'
            ]);
        }
    }

    // Helper methods for generating realistic data
    private function generateProductName($categoryName, $businessType, $index)
    {
        $prefixes = [
            'herbal_supplements' => ['Premium', 'Natural', 'Organic', 'Pure', 'Ultra', 'Advanced'],
            'organic_products' => ['Certified Organic', 'Pure Organic', 'Raw Organic', 'Fresh Organic'],
            'ayurvedic_medicine' => ['Ayurvedic', 'Traditional', 'Authentic', 'Ancient'],
            'herbal_pharmacy' => ['Pharmaceutical', 'Clinical', 'Professional', 'Medical'],
            'traditional_medicine' => ['Traditional', 'Ancient', 'Classical', 'Time-Tested']
        ];

        $suffixes = [
            'herbal_supplements' => ['Supplement', 'Formula', 'Complex', 'Blend', 'Extract'],
            'organic_products' => ['Powder', 'Oil', 'Tea', 'Capsules', 'Extract'],
            'ayurvedic_medicine' => ['Churna', 'Vati', 'Ras', 'Bhasma', 'Tailam'],
            'herbal_pharmacy' => ['Tablets', 'Capsules', 'Syrup', 'Ointment', 'Drops'],
            'traditional_medicine' => ['Formula', 'Decoction', 'Pills', 'Powder', 'Tonic']
        ];

        $prefix = $prefixes[$businessType][array_rand($prefixes[$businessType])];
        $suffix = $suffixes[$businessType][array_rand($suffixes[$businessType])];
        
        return $prefix . ' ' . $categoryName . ' ' . $suffix . ' #' . $index;
    }

    private function generatePrice($businessType)
    {
        $priceRanges = [
            'herbal_supplements' => [15, 80],
            'organic_products' => [12, 60],
            'ayurvedic_medicine' => [10, 100],
            'herbal_pharmacy' => [20, 150],
            'traditional_medicine' => [25, 120]
        ];

        $range = $priceRanges[$businessType];
        return rand($range[0] * 100, $range[1] * 100) / 100;
    }

    private function generateProductDescription($productName, $businessType)
    {
        $descriptions = [
            'herbal_supplements' => "This premium {$productName} is carefully formulated using the finest natural ingredients. Our supplement supports your daily wellness routine with scientifically-backed herbal extracts. Made with organic herbs and free from artificial additives.",
            'organic_products' => "Certified organic {$productName} sourced from sustainable farms. This product maintains the highest quality standards and is free from pesticides, chemicals, and synthetic additives. Perfect for your natural lifestyle.",
            'ayurvedic_medicine' => "Authentic {$productName} prepared according to traditional Ayurvedic principles. This time-tested formulation supports holistic wellness and is made using classical preparation methods with pure, natural ingredients.",
            'herbal_pharmacy' => "Professional-grade {$productName} formulated for therapeutic use. This pharmaceutical-quality product meets strict quality standards and is designed for effective health support.",
            'traditional_medicine' => "Traditional {$productName} based on ancient wisdom and time-tested formulations. This product combines traditional knowledge with modern quality assurance for optimal effectiveness."
        ];

        return $descriptions[$businessType] ?? $descriptions['herbal_supplements'];
    }

    private function generateShortDescription($productName)
    {
        return "High-quality {$productName} for natural health and wellness support.";
    }

    private function generateTags($businessType)
    {
        $tags = [
            'herbal_supplements' => 'natural, herbal, supplement, wellness, organic, health',
            'organic_products' => 'organic, natural, certified, eco-friendly, sustainable, pure',
            'ayurvedic_medicine' => 'ayurveda, traditional, holistic, natural, authentic, wellness',
            'herbal_pharmacy' => 'pharmaceutical, medical, therapeutic, clinical, professional, health',
            'traditional_medicine' => 'traditional, ancient, classical, natural, herbal, wellness'
        ];

        return $tags[$businessType] ?? $tags['herbal_supplements'];
    }
}
