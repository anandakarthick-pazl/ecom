<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin\Theme;

class ThemeSeeder extends Seeder
{
    public function run()
    {
        $themes = [
            [
                'name' => 'Fashion Store',
                'slug' => 'fashion-store',
                'description' => 'Modern and elegant theme perfect for clothing and fashion stores',
                'category' => 'clothing',
                'price' => 0,
                'is_free' => true,
                'features' => ['Responsive Design', 'Product Gallery', 'Wishlist', 'Quick View'],
                'status' => 'active'
            ],
            [
                'name' => 'Electronics Hub',
                'slug' => 'electronics-hub',
                'description' => 'High-tech theme designed for electronics and gadget stores',
                'category' => 'electronics',
                'price' => 49.99,
                'is_free' => false,
                'features' => ['Product Comparison', 'Reviews & Ratings', 'Advanced Filters', 'Tech Specs Display'],
                'status' => 'active'
            ],
            [
                'name' => 'Fresh Market',
                'slug' => 'fresh-market',
                'description' => 'Clean and fresh theme ideal for grocery and food stores',
                'category' => 'grocery',
                'price' => 39.99,
                'is_free' => false,
                'features' => ['Recipe Integration', 'Nutrition Facts', 'Delivery Zones', 'Fresh Produce Gallery'],
                'status' => 'active'
            ],
            [
                'name' => 'Book Haven',
                'slug' => 'book-haven',
                'description' => 'Literary-inspired theme perfect for bookstores and libraries',
                'category' => 'books',
                'price' => 29.99,
                'is_free' => false,
                'features' => ['Author Profiles', 'Book Reviews', 'Reading Lists', 'Preview Pages'],
                'status' => 'active'
            ],
            [
                'name' => 'Jewelry Boutique',
                'slug' => 'jewelry-boutique',
                'description' => 'Luxurious theme designed for jewelry and accessories stores',
                'category' => 'jewelry',
                'price' => 59.99,
                'is_free' => false,
                'features' => ['360° Product View', 'Custom Engravings', 'Size Guide', 'Luxury Gallery'],
                'status' => 'active'
            ],
            [
                'name' => 'Furniture Studio',
                'slug' => 'furniture-studio',
                'description' => 'Sophisticated theme for furniture and home decor stores',
                'category' => 'furniture',
                'price' => 44.99,
                'is_free' => false,
                'features' => ['Room Visualizer', 'Material Samples', 'Delivery Calculator', 'Assembly Guides'],
                'status' => 'active'
            ],
            [
                'name' => 'Sports Arena',
                'slug' => 'sports-arena',
                'description' => 'Dynamic theme for sports equipment and outdoor gear',
                'category' => 'sports',
                'price' => 34.99,
                'is_free' => false,
                'features' => ['Team Colors', 'Size Charts', 'Activity Guides', 'Gear Recommendations'],
                'status' => 'active'
            ],
            [
                'name' => 'Beauty Palace',
                'slug' => 'beauty-palace',
                'description' => 'Elegant theme for beauty and cosmetics stores',
                'category' => 'beauty',
                'price' => 39.99,
                'is_free' => false,
                'features' => ['Skin Tone Matcher', 'Tutorial Videos', 'Ingredient List', 'Before/After Gallery'],
                'status' => 'active'
            ],
            [
                'name' => 'Auto Parts Pro',
                'slug' => 'auto-parts-pro',
                'description' => 'Professional theme for automotive parts and accessories',
                'category' => 'automotive',
                'price' => 49.99,
                'is_free' => false,
                'features' => ['Vehicle Finder', 'Compatibility Check', 'Installation Guides', 'Warranty Info'],
                'status' => 'active'
            ],
            [
                'name' => 'Universal Store',
                'slug' => 'universal-store',
                'description' => 'Versatile theme suitable for any type of general merchandise',
                'category' => 'general',
                'price' => 0,
                'is_free' => true,
                'features' => ['Multi-Category Support', 'Flexible Layout', 'Custom Sections', 'Easy Customization'],
                'status' => 'active'
            ]
        ];

        foreach ($themes as $theme) {
            Theme::create($theme);
        }
    }
}
