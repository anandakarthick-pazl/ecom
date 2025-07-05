<?php

namespace Database\Seeders;

use App\Models\SuperAdmin\Theme;
use Illuminate\Database\Seeder;

class EnhancedThemeSeeder extends Seeder
{
    public function run()
    {
        $themes = [
            [
                'name' => 'Elegant Fashion',
                'slug' => 'elegant-fashion',
                'description' => 'A sophisticated and modern theme perfect for high-end fashion and luxury brands. Features clean lines, elegant typography, and sophisticated color schemes.',
                'category' => 'fashion',
                'layout_type' => 'grid',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#000000',
                    'secondary' => '#ffffff',
                    'accent' => '#c9a96e',
                    'background' => '#f8f9fa',
                    'text' => '#333333'
                ],
                'features' => [
                    'Responsive Design',
                    'Product Quick View',
                    'Wishlist Integration',
                    'Size Guide',
                    'Color Swatches',
                    'Zoom on Hover',
                    'Newsletter Signup',
                    'Social Media Integration'
                ],
                'components' => [
                    'Hero Banner',
                    'Featured Products',
                    'Category Grid',
                    'Product Cards',
                    'Shopping Cart',
                    'User Account',
                    'Search Bar',
                    'Footer'
                ],
                'tags' => ['fashion', 'elegant', 'luxury', 'modern', 'clean'],
                'difficulty_level' => 'beginner',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Theme Studio',
                'rating' => 4.8,
                'downloads_count' => 1250,
                'status' => 'active'
            ],
            [
                'name' => 'TechVibe Electronics',
                'slug' => 'techvibe-electronics',
                'description' => 'Dynamic and tech-focused theme with vibrant colors and modern UI elements. Perfect for electronics, gadgets, and tech stores.',
                'category' => 'electronics',
                'layout_type' => 'grid',
                'price' => 49.99,
                'is_free' => false,
                'color_scheme' => [
                    'primary' => '#007bff',
                    'secondary' => '#6c757d',
                    'accent' => '#28a745',
                    'background' => '#ffffff',
                    'text' => '#212529'
                ],
                'features' => [
                    'Product Comparison',
                    'Specifications Table',
                    'Tech Reviews',
                    'Video Galleries',
                    'Live Chat',
                    'Multi-Currency',
                    'Advanced Search',
                    'Newsletter'
                ],
                'components' => [
                    'Mega Menu',
                    'Product Slider',
                    'Comparison Table',
                    'Video Player',
                    'Review System',
                    'Chat Widget',
                    'Search Filters',
                    'Newsletter Form'
                ],
                'tags' => ['electronics', 'modern', 'tech', 'blue', 'professional'],
                'difficulty_level' => 'intermediate',
                'responsive' => true,
                'rtl_support' => false,
                'dark_mode' => true,
                'author' => 'TechThemes',
                'rating' => 4.7,
                'downloads_count' => 890,
                'status' => 'active'
            ],
            [
                'name' => 'Fresh Market',
                'slug' => 'fresh-market',
                'description' => 'Vibrant and fresh theme designed for food, beverages, and grocery stores. Features organic colors and appetizing layouts.',
                'category' => 'food',
                'layout_type' => 'grid',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#28a745',
                    'secondary' => '#17a2b8',
                    'accent' => '#ffc107',
                    'background' => '#f8f9fa',
                    'text' => '#495057'
                ],
                'features' => [
                    'Recipe Integration',
                    'Nutritional Info',
                    'Fresh Delivery',
                    'Organic Badges',
                    'Meal Planning',
                    'Seasonal Products',
                    'Bulk Orders',
                    'Farm Stories'
                ],
                'components' => [
                    'Category Cards',
                    'Product Grid',
                    'Recipe Cards',
                    'Delivery Info',
                    'Nutrition Facts',
                    'Seasonal Banner',
                    'Bulk Calculator',
                    'Story Section'
                ],
                'tags' => ['food', 'fresh', 'green', 'organic', 'healthy'],
                'difficulty_level' => 'beginner',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'FreshDesigns',
                'rating' => 4.6,
                'downloads_count' => 2100,
                'status' => 'active'
            ],
            [
                'name' => 'Beauty Bloom',
                'slug' => 'beauty-bloom',
                'description' => 'Elegant and feminine theme perfect for beauty, cosmetics, and skincare brands. Features soft pastels and luxurious design elements.',
                'category' => 'beauty',
                'layout_type' => 'masonry',
                'price' => 39.99,
                'is_free' => false,
                'color_scheme' => [
                    'primary' => '#e91e63',
                    'secondary' => '#f8bbd9',
                    'accent' => '#ff6b6b',
                    'background' => '#fdf2f8',
                    'text' => '#4a4a4a'
                ],
                'features' => [
                    'Makeup Tutorials',
                    'Skin Analysis',
                    'Beauty Tips',
                    'Virtual Try-On',
                    'Ingredient List',
                    'Age-Specific',
                    'Seasonal Collection',
                    'Loyalty Program'
                ],
                'components' => [
                    'Beauty Gallery',
                    'Tutorial Videos',
                    'Skin Quiz',
                    'Try-On Widget',
                    'Ingredient Info',
                    'Age Filter',
                    'Collection Slider',
                    'Loyalty Widget'
                ],
                'tags' => ['beauty', 'feminine', 'pink', 'elegant', 'soft'],
                'difficulty_level' => 'intermediate',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Beauty Studio',
                'rating' => 4.9,
                'downloads_count' => 1450,
                'status' => 'active'
            ],
            [
                'name' => 'Cozy Home',
                'slug' => 'cozy-home',
                'description' => 'Warm and inviting theme for home decor, furniture, and garden products. Features earthy tones and comfortable layouts.',
                'category' => 'home',
                'layout_type' => 'grid',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#8b4513',
                    'secondary' => '#d2691e',
                    'accent' => '#32cd32',
                    'background' => '#faf0e6',
                    'text' => '#2f4f4f'
                ],
                'features' => [
                    'Room Inspiration',
                    'Furniture Sets',
                    'Decorating Tips',
                    'Seasonal Decor',
                    'Plant Care Guide',
                    'Design Consultation',
                    'Bulk Discounts',
                    'Installation Service'
                ],
                'components' => [
                    'Room Gallery',
                    'Furniture Sets',
                    'Tip Cards',
                    'Seasonal Banner',
                    'Plant Guide',
                    'Consultation Form',
                    'Bulk Calculator',
                    'Service Info'
                ],
                'tags' => ['home', 'cozy', 'brown', 'warm', 'natural'],
                'difficulty_level' => 'beginner',
                'responsive' => true,
                'rtl_support' => false,
                'dark_mode' => false,
                'author' => 'HomeDesign Co',
                'rating' => 4.5,
                'downloads_count' => 1780,
                'status' => 'active'
            ],
            [
                'name' => 'Active Sports',
                'slug' => 'active-sports',
                'description' => 'Dynamic and energetic theme for sports, fitness, and outdoor gear. Features bold colors and action-packed layouts.',
                'category' => 'sports',
                'layout_type' => 'grid',
                'price' => 59.99,
                'is_free' => false,
                'color_scheme' => [
                    'primary' => '#ff4500',
                    'secondary' => '#1e90ff',
                    'accent' => '#32cd32',
                    'background' => '#ffffff',
                    'text' => '#333333'
                ],
                'features' => [
                    'Workout Videos',
                    'Fitness Tracker',
                    'Training Plans',
                    'Size Charts',
                    'Performance Metrics',
                    'Team Discounts',
                    'Event Calendar',
                    'Athlete Stories'
                ],
                'components' => [
                    'Hero Video',
                    'Product Grid',
                    'Fitness Tracker',
                    'Training Cards',
                    'Size Guide',
                    'Metrics Dashboard',
                    'Team Portal',
                    'Event List'
                ],
                'tags' => ['sports', 'dynamic', 'orange', 'energy', 'fitness'],
                'difficulty_level' => 'advanced',
                'responsive' => true,
                'rtl_support' => false,
                'dark_mode' => true,
                'author' => 'SportsTech',
                'rating' => 4.8,
                'downloads_count' => 950,
                'status' => 'active'
            ],
            [
                'name' => 'Luxury Jewelry',
                'slug' => 'luxury-jewelry',
                'description' => 'Sophisticated and premium theme for jewelry, watches, and luxury accessories. Features gold accents and elegant typography.',
                'category' => 'jewelry',
                'layout_type' => 'grid',
                'price' => 79.99,
                'is_free' => false,
                'color_scheme' => [
                    'primary' => '#2c3e50',
                    'secondary' => '#34495e',
                    'accent' => '#f39c12',
                    'background' => '#ffffff',
                    'text' => '#2c3e50'
                ],
                'features' => [
                    'Jewelry Customization',
                    'Gemstone Guide',
                    'Certification Info',
                    'Ring Sizer',
                    'Engraving Options',
                    'Insurance Info',
                    'Care Instructions',
                    'Authentication'
                ],
                'components' => [
                    'Luxury Gallery',
                    'Customization Tool',
                    'Gemstone Info',
                    'Certificate Viewer',
                    'Ring Sizer',
                    'Engraving Form',
                    'Insurance Calculator',
                    'Care Guide'
                ],
                'tags' => ['jewelry', 'luxury', 'gold', 'elegant', 'premium'],
                'difficulty_level' => 'expert',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Luxury Themes',
                'rating' => 4.9,
                'downloads_count' => 450,
                'status' => 'active'
            ],
            [
                'name' => 'Minimalist Store',
                'slug' => 'minimalist-store',
                'description' => 'Clean and minimal theme focusing on simplicity and user experience. Perfect for any modern ecommerce store.',
                'category' => 'minimal',
                'layout_type' => 'grid',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#212529',
                    'secondary' => '#6c757d',
                    'accent' => '#007bff',
                    'background' => '#ffffff',
                    'text' => '#495057'
                ],
                'features' => [
                    'Clean Design',
                    'Fast Loading',
                    'Mobile Optimized',
                    'SEO Friendly',
                    'Accessibility',
                    'Multi-Language',
                    'Simple Checkout',
                    'Clean Code'
                ],
                'components' => [
                    'Simple Header',
                    'Product Grid',
                    'Clean Cards',
                    'Minimal Footer',
                    'Simple Search',
                    'Basic Cart',
                    'User Menu',
                    'Breadcrumbs'
                ],
                'tags' => ['minimal', 'clean', 'simple', 'modern', 'fast'],
                'difficulty_level' => 'beginner',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => true,
                'author' => 'Minimal Studio',
                'rating' => 4.7,
                'downloads_count' => 3200,
                'status' => 'active'
            ],
            [
                'name' => 'Vintage Market',
                'slug' => 'vintage-market',
                'description' => 'Retro and vintage-inspired theme with classic design elements. Perfect for antiques, vintage clothing, and nostalgic products.',
                'category' => 'vintage',
                'layout_type' => 'masonry',
                'price' => 35.99,
                'is_free' => false,
                'color_scheme' => [
                    'primary' => '#8b4513',
                    'secondary' => '#cd853f',
                    'accent' => '#daa520',
                    'background' => '#fdf5e6',
                    'text' => '#654321'
                ],
                'features' => [
                    'Vintage Filters',
                    'Era Categories',
                    'Authenticity Badges',
                    'Restoration Tips',
                    'History Stories',
                    'Collector Items',
                    'Vintage Blog',
                    'Appraisal Service'
                ],
                'components' => [
                    'Vintage Gallery',
                    'Era Filter',
                    'Authenticity Badge',
                    'Restoration Guide',
                    'History Cards',
                    'Collector Badge',
                    'Vintage Blog',
                    'Appraisal Form'
                ],
                'tags' => ['vintage', 'retro', 'classic', 'antique', 'nostalgic'],
                'difficulty_level' => 'intermediate',
                'responsive' => true,
                'rtl_support' => false,
                'dark_mode' => false,
                'author' => 'Vintage Designs',
                'rating' => 4.6,
                'downloads_count' => 720,
                'status' => 'active'
            ],
            [
                'name' => 'Modern Pharmacy',
                'slug' => 'modern-pharmacy',
                'description' => 'Professional and trustworthy theme for pharmacy, medical supplies, and healthcare products. Features medical colors and clean layouts.',
                'category' => 'pharmacy',
                'layout_type' => 'list',
                'price' => 89.99,
                'is_free' => false,
                'color_scheme' => [
                    'primary' => '#0066cc',
                    'secondary' => '#28a745',
                    'accent' => '#17a2b8',
                    'background' => '#ffffff',
                    'text' => '#333333'
                ],
                'features' => [
                    'Prescription Upload',
                    'Medicine Search',
                    'Dosage Calculator',
                    'Drug Interactions',
                    'Prescription Refills',
                    'Insurance Claims',
                    'Consultation Booking',
                    'Health Records'
                ],
                'components' => [
                    'Prescription Form',
                    'Medicine Finder',
                    'Dosage Tool',
                    'Interaction Checker',
                    'Refill Manager',
                    'Insurance Portal',
                    'Consultation Calendar',
                    'Health Dashboard'
                ],
                'tags' => ['pharmacy', 'medical', 'healthcare', 'professional', 'blue'],
                'difficulty_level' => 'expert',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'MedTech Solutions',
                'rating' => 4.8,
                'downloads_count' => 380,
                'status' => 'active'
            ],
            [
                'name' => 'Colorful Toys',
                'slug' => 'colorful-toys',
                'description' => 'Bright and playful theme designed for toy stores and children\'s products. Features vibrant colors and fun animations.',
                'category' => 'toys',
                'layout_type' => 'grid',
                'price' => 29.99,
                'is_free' => false,
                'color_scheme' => [
                    'primary' => '#ff6b6b',
                    'secondary' => '#4ecdc4',
                    'accent' => '#ffe66d',
                    'background' => '#ffffff',
                    'text' => '#333333'
                ],
                'features' => [
                    'Age Categories',
                    'Safety Information',
                    'Educational Value',
                    'Parental Controls',
                    'Wish Lists',
                    'Gift Wrapping',
                    'Party Packages',
                    'Toy Reviews'
                ],
                'components' => [
                    'Age Filter',
                    'Safety Badge',
                    'Educational Info',
                    'Parental Dashboard',
                    'Wish List',
                    'Gift Options',
                    'Party Planner',
                    'Review System'
                ],
                'tags' => ['toys', 'colorful', 'playful', 'children', 'fun'],
                'difficulty_level' => 'intermediate',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Playful Designs',
                'rating' => 4.7,
                'downloads_count' => 1120,
                'status' => 'active'
            ],
            [
                'name' => 'Artisan Crafts',
                'slug' => 'artisan-crafts',
                'description' => 'Creative and artistic theme for handmade crafts, art supplies, and creative products. Features earthy tones and artistic layouts.',
                'category' => 'art',
                'layout_type' => 'masonry',
                'price' => 45.99,
                'is_free' => false,
                'color_scheme' => [
                    'primary' => '#8b4513',
                    'secondary' => '#cd853f',
                    'accent' => '#ff6347',
                    'background' => '#faf0e6',
                    'text' => '#2f4f4f'
                ],
                'features' => [
                    'Artist Profiles',
                    'Craft Tutorials',
                    'Material Guide',
                    'Custom Orders',
                    'Workshop Bookings',
                    'Skill Levels',
                    'Project Ideas',
                    'Community Gallery'
                ],
                'components' => [
                    'Artist Gallery',
                    'Tutorial Videos',
                    'Material List',
                    'Custom Form',
                    'Workshop Calendar',
                    'Skill Filter',
                    'Project Cards',
                    'Community Hub'
                ],
                'tags' => ['art', 'crafts', 'handmade', 'creative', 'artistic'],
                'difficulty_level' => 'intermediate',
                'responsive' => true,
                'rtl_support' => false,
                'dark_mode' => false,
                'author' => 'Artisan Studio',
                'rating' => 4.8,
                'downloads_count' => 650,
                'status' => 'active'
            ]
        ];

        foreach ($themes as $themeData) {
            Theme::create($themeData);
        }
    }
}
