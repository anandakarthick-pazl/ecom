<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin\Theme;
use Illuminate\Support\Str;

class ModernEcomThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Vibrant Marketplace',
                'description' => 'A bold and vibrant theme perfect for modern marketplaces with gradient backgrounds and dynamic animations.',
                'category' => 'general',
                'layout_type' => 'grid',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#FF6B6B',
                    'secondary' => '#4ECDC4',
                    'accent' => '#45B7D1',
                    'background' => '#F8F9FA',
                    'text' => '#2C3E50',
                    'gradient' => 'linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%)'
                ],
                'features' => [
                    'Animated Product Cards',
                    'Gradient Backgrounds',
                    'Modern Typography',
                    'Responsive Grid Layout',
                    'Interactive Buttons',
                    'Color-coded Categories',
                    'Dynamic Hover Effects'
                ],
                'tags' => ['modern', 'colorful', 'vibrant', 'animated', 'responsive'],
                'components' => [
                    'hero_section' => [
                        'background' => 'gradient',
                        'text_color' => 'white',
                        'button_style' => 'rounded-full',
                        'animation' => 'fade-in-up'
                    ],
                    'product_grid' => [
                        'columns' => 4,
                        'card_style' => 'elevated',
                        'hover_effect' => 'scale-up',
                        'border_radius' => '15px'
                    ],
                    'navbar' => [
                        'style' => 'transparent',
                        'position' => 'fixed',
                        'background' => 'rgba(255,255,255,0.95)'
                    ]
                ],
                'difficulty_level' => 'intermediate',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => true,
                'author' => 'Modern Themes Studio',
                'status' => 'active'
            ],
            [
                'name' => 'Neon Glow Store',
                'description' => 'A futuristic theme with neon colors and glowing effects, perfect for tech and electronics stores.',
                'category' => 'electronics',
                'layout_type' => 'grid',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#00D4FF',
                    'secondary' => '#FF0080',
                    'accent' => '#39FF14',
                    'background' => '#0A0A0A',
                    'text' => '#FFFFFF',
                    'gradient' => 'linear-gradient(135deg, #00D4FF 0%, #FF0080 100%)',
                    'glow' => '0 0 20px rgba(0, 212, 255, 0.5)'
                ],
                'features' => [
                    'Neon Glow Effects',
                    'Dark Theme Design',
                    'Futuristic Typography',
                    'Animated Borders',
                    'Glowing Buttons',
                    'Cyber-style Navigation',
                    'RGB Color Transitions'
                ],
                'tags' => ['neon', 'futuristic', 'dark', 'glowing', 'tech'],
                'components' => [
                    'hero_section' => [
                        'background' => 'dark-gradient',
                        'text_color' => 'neon-blue',
                        'button_style' => 'neon-glow',
                        'animation' => 'neon-pulse'
                    ],
                    'product_grid' => [
                        'columns' => 3,
                        'card_style' => 'neon-border',
                        'hover_effect' => 'glow-up',
                        'border_radius' => '10px'
                    ],
                    'navbar' => [
                        'style' => 'dark-transparent',
                        'position' => 'fixed',
                        'background' => 'rgba(10,10,10,0.9)'
                    ]
                ],
                'difficulty_level' => 'advanced',
                'responsive' => true,
                'rtl_support' => false,
                'dark_mode' => true,
                'author' => 'Cyber Themes',
                'status' => 'active'
            ],
            [
                'name' => 'Pastel Dream',
                'description' => 'A soft and elegant theme with pastel colors, perfect for beauty, fashion, and lifestyle brands.',
                'category' => 'beauty',
                'layout_type' => 'masonry',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#FFB6C1',
                    'secondary' => '#E0E6FF',
                    'accent' => '#98FB98',
                    'background' => '#FFFAF0',
                    'text' => '#4A4A4A',
                    'gradient' => 'linear-gradient(135deg, #FFB6C1 0%, #E0E6FF 50%, #98FB98 100%)'
                ],
                'features' => [
                    'Soft Pastel Colors',
                    'Elegant Typography',
                    'Masonry Layout',
                    'Smooth Animations',
                    'Minimalist Design',
                    'Pinterest-style Grid',
                    'Delicate Shadows'
                ],
                'tags' => ['pastel', 'elegant', 'soft', 'beauty', 'minimalist'],
                'components' => [
                    'hero_section' => [
                        'background' => 'pastel-gradient',
                        'text_color' => 'soft-gray',
                        'button_style' => 'soft-rounded',
                        'animation' => 'gentle-fade'
                    ],
                    'product_grid' => [
                        'columns' => 'masonry',
                        'card_style' => 'soft-shadow',
                        'hover_effect' => 'gentle-lift',
                        'border_radius' => '20px'
                    ],
                    'navbar' => [
                        'style' => 'light-elegant',
                        'position' => 'sticky',
                        'background' => 'rgba(255,250,240,0.95)'
                    ]
                ],
                'difficulty_level' => 'beginner',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Elegant Designs',
                'status' => 'active'
            ],
            [
                'name' => 'Tropical Paradise',
                'description' => 'A vibrant tropical theme with bright colors and nature-inspired elements, perfect for travel and lifestyle brands.',
                'category' => 'travel',
                'layout_type' => 'fullwidth',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#FF7F50',
                    'secondary' => '#20B2AA',
                    'accent' => '#FFD700',
                    'background' => '#F0FFFF',
                    'text' => '#2F4F4F',
                    'gradient' => 'linear-gradient(135deg, #FF7F50 0%, #20B2AA 50%, #FFD700 100%)'
                ],
                'features' => [
                    'Tropical Color Scheme',
                    'Nature-inspired Icons',
                    'Full-width Layouts',
                    'Parallax Scrolling',
                    'Beach-themed Elements',
                    'Organic Shapes',
                    'Sunset Gradients'
                ],
                'tags' => ['tropical', 'nature', 'travel', 'bright', 'organic'],
                'components' => [
                    'hero_section' => [
                        'background' => 'tropical-gradient',
                        'text_color' => 'deep-teal',
                        'button_style' => 'tropical-wave',
                        'animation' => 'wave-in'
                    ],
                    'product_grid' => [
                        'columns' => 4,
                        'card_style' => 'tropical-card',
                        'hover_effect' => 'wave-bounce',
                        'border_radius' => '25px'
                    ],
                    'navbar' => [
                        'style' => 'tropical-nav',
                        'position' => 'fixed',
                        'background' => 'rgba(240,255,255,0.9)'
                    ]
                ],
                'difficulty_level' => 'intermediate',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Tropical Themes',
                'status' => 'active'
            ],
            [
                'name' => 'Retro Synthwave',
                'description' => 'An 80s-inspired theme with synthwave colors and retro-futuristic elements.',
                'category' => 'music',
                'layout_type' => 'grid',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#FF0080',
                    'secondary' => '#00FFFF',
                    'accent' => '#FFFF00',
                    'background' => '#1a0d2e',
                    'text' => '#FFFFFF',
                    'gradient' => 'linear-gradient(135deg, #FF0080 0%, #00FFFF 50%, #FFFF00 100%)'
                ],
                'features' => [
                    'Synthwave Color Palette',
                    '80s Inspired Design',
                    'Retro Typography',
                    'Neon Grid Lines',
                    'Vintage Effects',
                    'Laser Beam Animations',
                    'Glitch Effects'
                ],
                'tags' => ['retro', '80s', 'synthwave', 'neon', 'vintage'],
                'components' => [
                    'hero_section' => [
                        'background' => 'synthwave-gradient',
                        'text_color' => 'neon-white',
                        'button_style' => 'retro-glow',
                        'animation' => 'laser-sweep'
                    ],
                    'product_grid' => [
                        'columns' => 3,
                        'card_style' => 'retro-grid',
                        'hover_effect' => 'glitch-hover',
                        'border_radius' => '5px'
                    ],
                    'navbar' => [
                        'style' => 'retro-nav',
                        'position' => 'fixed',
                        'background' => 'rgba(26,13,46,0.9)'
                    ]
                ],
                'difficulty_level' => 'advanced',
                'responsive' => true,
                'rtl_support' => false,
                'dark_mode' => true,
                'author' => 'Retro Studios',
                'status' => 'active'
            ],
            [
                'name' => 'Sunset Orange',
                'description' => 'A warm and inviting theme with sunset-inspired orange gradients, perfect for food and lifestyle brands.',
                'category' => 'food',
                'layout_type' => 'grid',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#FF8C00',
                    'secondary' => '#FF6347',
                    'accent' => '#FFD700',
                    'background' => '#FFF8DC',
                    'text' => '#8B4513',
                    'gradient' => 'linear-gradient(135deg, #FF8C00 0%, #FF6347 50%, #FFD700 100%)'
                ],
                'features' => [
                    'Sunset Color Palette',
                    'Warm Typography',
                    'Appetizing Design',
                    'Cozy Atmosphere',
                    'Food-friendly Layout',
                    'Warm Shadows',
                    'Organic Buttons'
                ],
                'tags' => ['sunset', 'warm', 'food', 'cozy', 'organic'],
                'components' => [
                    'hero_section' => [
                        'background' => 'sunset-gradient',
                        'text_color' => 'warm-brown',
                        'button_style' => 'warm-rounded',
                        'animation' => 'warm-glow'
                    ],
                    'product_grid' => [
                        'columns' => 4,
                        'card_style' => 'warm-card',
                        'hover_effect' => 'warm-lift',
                        'border_radius' => '15px'
                    ],
                    'navbar' => [
                        'style' => 'warm-nav',
                        'position' => 'sticky',
                        'background' => 'rgba(255,248,220,0.95)'
                    ]
                ],
                'difficulty_level' => 'beginner',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Sunset Designs',
                'status' => 'active'
            ],
            [
                'name' => 'Ocean Breeze',
                'description' => 'A calming blue theme inspired by ocean waves and sea breeze, perfect for wellness and lifestyle brands.',
                'category' => 'health',
                'layout_type' => 'fullwidth',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#1E90FF',
                    'secondary' => '#00CED1',
                    'accent' => '#87CEEB',
                    'background' => '#F0F8FF',
                    'text' => '#2F4F4F',
                    'gradient' => 'linear-gradient(135deg, #1E90FF 0%, #00CED1 50%, #87CEEB 100%)'
                ],
                'features' => [
                    'Ocean-inspired Colors',
                    'Wave Animations',
                    'Calming Design',
                    'Fluid Layouts',
                    'Wellness-focused',
                    'Serene Typography',
                    'Peaceful Atmosphere'
                ],
                'tags' => ['ocean', 'calm', 'wellness', 'blue', 'peaceful'],
                'components' => [
                    'hero_section' => [
                        'background' => 'ocean-gradient',
                        'text_color' => 'deep-blue',
                        'button_style' => 'wave-button',
                        'animation' => 'wave-motion'
                    ],
                    'product_grid' => [
                        'columns' => 3,
                        'card_style' => 'ocean-card',
                        'hover_effect' => 'wave-hover',
                        'border_radius' => '20px'
                    ],
                    'navbar' => [
                        'style' => 'ocean-nav',
                        'position' => 'fixed',
                        'background' => 'rgba(240,248,255,0.9)'
                    ]
                ],
                'difficulty_level' => 'intermediate',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Ocean Themes',
                'status' => 'active'
            ],
            [
                'name' => 'Galaxy Purple',
                'description' => 'A mystical purple theme with galaxy-inspired elements and cosmic animations.',
                'category' => 'luxury',
                'layout_type' => 'masonry',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#8A2BE2',
                    'secondary' => '#9932CC',
                    'accent' => '#DA70D6',
                    'background' => '#191970',
                    'text' => '#FFFFFF',
                    'gradient' => 'linear-gradient(135deg, #8A2BE2 0%, #9932CC 50%, #DA70D6 100%)'
                ],
                'features' => [
                    'Galaxy Color Scheme',
                    'Cosmic Animations',
                    'Mystical Elements',
                    'Starry Backgrounds',
                    'Luxury Typography',
                    'Ethereal Effects',
                    'Premium Feel'
                ],
                'tags' => ['galaxy', 'purple', 'luxury', 'cosmic', 'premium'],
                'components' => [
                    'hero_section' => [
                        'background' => 'galaxy-gradient',
                        'text_color' => 'cosmic-white',
                        'button_style' => 'galaxy-glow',
                        'animation' => 'star-twinkle'
                    ],
                    'product_grid' => [
                        'columns' => 'masonry',
                        'card_style' => 'galaxy-card',
                        'hover_effect' => 'cosmic-hover',
                        'border_radius' => '15px'
                    ],
                    'navbar' => [
                        'style' => 'galaxy-nav',
                        'position' => 'fixed',
                        'background' => 'rgba(25,25,112,0.9)'
                    ]
                ],
                'difficulty_level' => 'advanced',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => true,
                'author' => 'Galaxy Designs',
                'status' => 'active'
            ],
            [
                'name' => 'Forest Green',
                'description' => 'An eco-friendly theme with natural green tones and organic elements, perfect for sustainable and eco brands.',
                'category' => 'home',
                'layout_type' => 'grid',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#228B22',
                    'secondary' => '#32CD32',
                    'accent' => '#90EE90',
                    'background' => '#F5FFFA',
                    'text' => '#2F4F2F',
                    'gradient' => 'linear-gradient(135deg, #228B22 0%, #32CD32 50%, #90EE90 100%)'
                ],
                'features' => [
                    'Eco-friendly Colors',
                    'Natural Typography',
                    'Organic Shapes',
                    'Sustainable Design',
                    'Green Animations',
                    'Earth-friendly Layout',
                    'Nature-inspired Icons'
                ],
                'tags' => ['green', 'eco', 'natural', 'sustainable', 'organic'],
                'components' => [
                    'hero_section' => [
                        'background' => 'forest-gradient',
                        'text_color' => 'forest-green',
                        'button_style' => 'eco-button',
                        'animation' => 'leaf-sway'
                    ],
                    'product_grid' => [
                        'columns' => 4,
                        'card_style' => 'eco-card',
                        'hover_effect' => 'nature-hover',
                        'border_radius' => '10px'
                    ],
                    'navbar' => [
                        'style' => 'eco-nav',
                        'position' => 'sticky',
                        'background' => 'rgba(245,255,250,0.95)'
                    ]
                ],
                'difficulty_level' => 'beginner',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Eco Themes',
                'status' => 'active'
            ],
            [
                'name' => 'Electric Blue',
                'description' => 'A high-energy theme with electric blue colors and dynamic animations, perfect for sports and fitness brands.',
                'category' => 'sports',
                'layout_type' => 'grid',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#0080FF',
                    'secondary' => '#00BFFF',
                    'accent' => '#87CEFA',
                    'background' => '#F0F8FF',
                    'text' => '#003366',
                    'gradient' => 'linear-gradient(135deg, #0080FF 0%, #00BFFF 50%, #87CEFA 100%)'
                ],
                'features' => [
                    'Electric Blue Theme',
                    'High Energy Design',
                    'Dynamic Animations',
                    'Athletic Typography',
                    'Sports-focused Layout',
                    'Performance Metrics',
                    'Action-oriented Elements'
                ],
                'tags' => ['electric', 'blue', 'sports', 'dynamic', 'energy'],
                'components' => [
                    'hero_section' => [
                        'background' => 'electric-gradient',
                        'text_color' => 'electric-blue',
                        'button_style' => 'electric-button',
                        'animation' => 'electric-pulse'
                    ],
                    'product_grid' => [
                        'columns' => 3,
                        'card_style' => 'electric-card',
                        'hover_effect' => 'electric-hover',
                        'border_radius' => '12px'
                    ],
                    'navbar' => [
                        'style' => 'electric-nav',
                        'position' => 'fixed',
                        'background' => 'rgba(240,248,255,0.9)'
                    ]
                ],
                'difficulty_level' => 'intermediate',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Electric Themes',
                'status' => 'active'
            ],
            [
                'name' => 'Rose Gold Luxury',
                'description' => 'An elegant luxury theme with rose gold accents and premium styling, perfect for high-end brands.',
                'category' => 'jewelry',
                'layout_type' => 'masonry',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#E8B4B8',
                    'secondary' => '#D4A574',
                    'accent' => '#F7E7CE',
                    'background' => '#FEFEFE',
                    'text' => '#5D4037',
                    'gradient' => 'linear-gradient(135deg, #E8B4B8 0%, #D4A574 50%, #F7E7CE 100%)'
                ],
                'features' => [
                    'Rose Gold Color Scheme',
                    'Luxury Typography',
                    'Premium Animations',
                    'Elegant Design',
                    'High-end Layout',
                    'Sophisticated Effects',
                    'Refined Details'
                ],
                'tags' => ['rose-gold', 'luxury', 'premium', 'elegant', 'sophisticated'],
                'components' => [
                    'hero_section' => [
                        'background' => 'rose-gold-gradient',
                        'text_color' => 'luxury-brown',
                        'button_style' => 'luxury-button',
                        'animation' => 'luxury-shine'
                    ],
                    'product_grid' => [
                        'columns' => 'masonry',
                        'card_style' => 'luxury-card',
                        'hover_effect' => 'luxury-hover',
                        'border_radius' => '8px'
                    ],
                    'navbar' => [
                        'style' => 'luxury-nav',
                        'position' => 'fixed',
                        'background' => 'rgba(254,254,254,0.95)'
                    ]
                ],
                'difficulty_level' => 'advanced',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Luxury Themes',
                'status' => 'active'
            ],
            [
                'name' => 'Mint Fresh',
                'description' => 'A fresh and clean theme with mint green colors and minimal design, perfect for health and wellness brands.',
                'category' => 'pharmacy',
                'layout_type' => 'grid',
                'price' => 0,
                'is_free' => true,
                'color_scheme' => [
                    'primary' => '#98FB98',
                    'secondary' => '#00FA9A',
                    'accent' => '#AFEEEE',
                    'background' => '#F0FFF0',
                    'text' => '#2F4F4F',
                    'gradient' => 'linear-gradient(135deg, #98FB98 0%, #00FA9A 50%, #AFEEEE 100%)'
                ],
                'features' => [
                    'Mint Green Theme',
                    'Fresh Design',
                    'Clean Layout',
                    'Minimal Typography',
                    'Health-focused',
                    'Calming Colors',
                    'Medical-friendly'
                ],
                'tags' => ['mint', 'fresh', 'clean', 'health', 'medical'],
                'components' => [
                    'hero_section' => [
                        'background' => 'mint-gradient',
                        'text_color' => 'mint-dark',
                        'button_style' => 'mint-button',
                        'animation' => 'mint-fresh'
                    ],
                    'product_grid' => [
                        'columns' => 4,
                        'card_style' => 'mint-card',
                        'hover_effect' => 'mint-hover',
                        'border_radius' => '12px'
                    ],
                    'navbar' => [
                        'style' => 'mint-nav',
                        'position' => 'sticky',
                        'background' => 'rgba(240,255,240,0.95)'
                    ]
                ],
                'difficulty_level' => 'beginner',
                'responsive' => true,
                'rtl_support' => true,
                'dark_mode' => false,
                'author' => 'Fresh Themes',
                'status' => 'active'
            ]
        ];

        foreach ($themes as $themeData) {
            $themeData['slug'] = Str::slug($themeData['name']);
            $themeData['rating'] = rand(35, 50) / 10; // Random rating between 3.5 and 5.0
            $themeData['downloads_count'] = rand(50, 500);
            
            // Set preview image path
            $previewImagePath = 'themes/' . $themeData['slug'] . '.jpg';
            if (file_exists(public_path('images/' . $previewImagePath))) {
                $themeData['preview_image'] = $previewImagePath;
            }
            
            Theme::create($themeData);
        }
    }
}
