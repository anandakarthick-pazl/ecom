<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Run Super Admin Seeders
        $this->call([
            SuperAdminSeeder::class,
            ThemeSeeder::class,
            PackageSeeder::class,
            LandingPageSeeder::class,
            CompleteSampleDataSeeder::class,
            PaymentMethodSeeder::class,
            UserManagementSeeder::class, // Add our new seeder
        ]);

        // Create admin user for existing system
        User::updateOrCreate(
            ['email' => 'admin@herbalbliss.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@herbalbliss.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_super_admin' => false,
                'role' => 'admin',
                'status' => 'active'
            ]
        );

        // Note: Sample data is now handled by migration 2024_12_29_000001_seed_complete_sample_data

        // Create categories
        $categories = [
            [
                'name' => 'Skincare',
                'description' => 'Natural and organic skincare products for healthy glowing skin',
                'meta_title' => 'Natural Skincare Products - Herbal Bliss',
                'meta_description' => 'Discover our range of natural skincare products made with pure herbal ingredients.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Hair Care',
                'description' => 'Herbal hair care solutions for healthy and beautiful hair',
                'meta_title' => 'Natural Hair Care Products - Herbal Bliss',
                'meta_description' => 'Nourish your hair with our natural herbal hair care products.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Herbal Teas',
                'description' => 'Premium quality herbal teas for wellness and relaxation',
                'meta_title' => 'Herbal Teas - Herbal Bliss',
                'meta_description' => 'Enjoy our collection of premium herbal teas for health and wellness.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Essential Oils',
                'description' => 'Pure essential oils for aromatherapy and natural healing',
                'meta_title' => 'Essential Oils - Herbal Bliss',
                'meta_description' => 'Pure and natural essential oils for aromatherapy and wellness.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Wellness',
                'description' => 'Natural wellness products for overall health and vitality',
                'meta_title' => 'Wellness Products - Herbal Bliss',
                'meta_description' => 'Natural wellness products to support your healthy lifestyle.',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create($categoryData);
            $this->createSampleProducts($category);
        }

        $this->createSampleBanners();
    }

    private function createSampleProducts($category)
    {
        $products = $this->getProductsByCategory($category->name);

        foreach ($products as $productData) {
            $productData['category_id'] = $category->id;
            Product::create($productData);
        }
    }

    private function getProductsByCategory($categoryName)
    {
        switch ($categoryName) {
            case 'Skincare':
                return [
                    [
                        'name' => 'Neem Face Wash',
                        'description' => 'Natural neem face wash that gently cleanses and purifies skin. Made with pure neem extract and aloe vera for daily use. Suitable for all skin types.',
                        'short_description' => 'Gentle neem face wash for clean and clear skin',
                        'price' => 299.00,
                        'discount_price' => 249.00,
                        'stock' => 50,
                        'sku' => 'SKN001',
                        'weight' => 100,
                        'weight_unit' => 'ml',
                        'meta_title' => 'Neem Face Wash - Natural Skincare',
                        'meta_description' => 'Gentle neem face wash for clean and healthy skin.',
                        'is_active' => true,
                        'is_featured' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Turmeric Face Pack',
                        'description' => 'Traditional turmeric face pack with natural ingredients. Helps brighten skin and reduce blemishes. Perfect for weekly skincare routine.',
                        'short_description' => 'Brightening turmeric face pack for glowing skin',
                        'price' => 399.00,
                        'stock' => 30,
                        'sku' => 'SKN002',
                        'weight' => 50,
                        'weight_unit' => 'gm',
                        'is_active' => true,
                        'is_featured' => false,
                        'sort_order' => 2,
                    ],
                ];
            
            case 'Hair Care':
                return [
                    [
                        'name' => 'Coconut Hair Oil',
                        'description' => 'Pure coconut hair oil enriched with herbs for strong and healthy hair. Regular use promotes hair growth and adds natural shine.',
                        'short_description' => 'Nourishing coconut oil for healthy hair',
                        'price' => 199.00,
                        'discount_price' => 159.00,
                        'stock' => 40,
                        'sku' => 'HAR001',
                        'weight' => 200,
                        'weight_unit' => 'ml',
                        'is_active' => true,
                        'is_featured' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Herbal Shampoo',
                        'description' => 'Gentle herbal shampoo made with natural ingredients. Free from harsh chemicals, perfect for daily use on all hair types.',
                        'short_description' => 'Chemical-free herbal shampoo',
                        'price' => 349.00,
                        'stock' => 25,
                        'sku' => 'HAR002',
                        'weight' => 250,
                        'weight_unit' => 'ml',
                        'is_active' => true,
                        'is_featured' => false,
                        'sort_order' => 2,
                    ],
                ];
            
            case 'Herbal Teas':
                return [
                    [
                        'name' => 'Green Tea with Tulsi',
                        'description' => 'Premium green tea blended with holy basil (tulsi) for enhanced wellness benefits. Rich in antioxidants and perfect for daily consumption.',
                        'short_description' => 'Antioxidant-rich green tea with tulsi',
                        'price' => 299.00,
                        'stock' => 60,
                        'sku' => 'TEA001',
                        'weight' => 100,
                        'weight_unit' => 'gm',
                        'is_active' => true,
                        'is_featured' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Chamomile Tea',
                        'description' => 'Soothing chamomile tea perfect for relaxation and better sleep. Made from premium chamomile flowers, naturally caffeine-free.',
                        'short_description' => 'Relaxing chamomile tea for better sleep',
                        'price' => 399.00,
                        'stock' => 35,
                        'sku' => 'TEA002',
                        'weight' => 50,
                        'weight_unit' => 'gm',
                        'is_active' => true,
                        'is_featured' => false,
                        'sort_order' => 2,
                    ],
                ];
            
            case 'Essential Oils':
                return [
                    [
                        'name' => 'Lavender Essential Oil',
                        'description' => 'Pure lavender essential oil for aromatherapy and relaxation. Known for its calming properties and beautiful fragrance.',
                        'short_description' => 'Calming lavender oil for relaxation',
                        'price' => 599.00,
                        'discount_price' => 499.00,
                        'stock' => 20,
                        'sku' => 'OIL001',
                        'weight' => 30,
                        'weight_unit' => 'ml',
                        'is_active' => true,
                        'is_featured' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Tea Tree Oil',
                        'description' => 'Pure tea tree essential oil with natural antibacterial properties. Great for skincare and natural healing applications.',
                        'short_description' => 'Antibacterial tea tree essential oil',
                        'price' => 699.00,
                        'stock' => 15,
                        'sku' => 'OIL002',
                        'weight' => 30,
                        'weight_unit' => 'ml',
                        'is_active' => true,
                        'is_featured' => false,
                        'sort_order' => 2,
                    ],
                ];
            
            case 'Wellness':
                return [
                    [
                        'name' => 'Ashwagandha Powder',
                        'description' => 'Pure ashwagandha powder to help manage stress and boost immunity. Made from premium quality ashwagandha roots.',
                        'short_description' => 'Natural stress relief with ashwagandha',
                        'price' => 799.00,
                        'discount_price' => 699.00,
                        'stock' => 30,
                        'sku' => 'WEL001',
                        'weight' => 200,
                        'weight_unit' => 'gm',
                        'is_active' => true,
                        'is_featured' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Moringa Powder',
                        'description' => 'Nutrient-rich moringa powder packed with vitamins and minerals. Perfect addition to smoothies and healthy recipes.',
                        'short_description' => 'Superfood moringa powder for nutrition',
                        'price' => 599.00,
                        'stock' => 25,
                        'sku' => 'WEL002',
                        'weight' => 100,
                        'weight_unit' => 'gm',
                        'is_active' => true,
                        'is_featured' => false,
                        'sort_order' => 2,
                    ],
                ];
            
            default:
                return [];
        }
    }

    private function createSampleBanners()
    {
        Banner::create([
            'title' => 'Welcome to Herbal Bliss',
            'image' => 'banners/welcome-banner.jpg',
            'link_url' => route('home'),
            'position' => 'top',
            'is_active' => true,
            'sort_order' => 1,
            'alt_text' => 'Welcome to Herbal Bliss - Natural Products',
        ]);

        Banner::create([
            'title' => 'Natural Skincare Collection',
            'image' => 'banners/skincare-banner.jpg',
            'link_url' => '#',
            'position' => 'top',
            'is_active' => true,
            'sort_order' => 2,
            'alt_text' => 'Natural Skincare Products',
        ]);
    }
}
