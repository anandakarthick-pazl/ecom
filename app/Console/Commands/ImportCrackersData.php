<?php

namespace App\Console\Commands;

use App\Services\CrackersDataExtractor;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportCrackersData extends Command
{
    protected $signature = 'crackers:import 
                          {--company-id=1 : Company ID to import data for}
                          {--skip-images : Skip downloading images}
                          {--force : Force import even if data exists}';
    
    protected $description = 'Import crackers data from thiruchendurmurugancrackers.com';
    
    public function handle()
    {
        $this->info('🎆 Starting Crackers Data Import...');
        
        $companyId = $this->option('company-id');
        $skipImages = $this->option('skip-images');
        $force = $this->option('force');
        
        // Check if data already exists
        if (!$force) {
            $existingCategories = Category::where('company_id', $companyId)->count();
            $existingProducts = Product::where('company_id', $companyId)->count();
            
            if ($existingCategories > 0 || $existingProducts > 0) {
                if (!$this->confirm("Data already exists for company ID {$companyId}. Continue?")) {
                    $this->info('Import cancelled.');
                    return;
                }
            }
        }
        
        // Initialize the data extractor
        $extractor = new CrackersDataExtractor();
        
        // Extract data from website
        $this->info('📥 Extracting data from website...');
        $data = $extractor->extractData();
        
        if (!$data) {
            $this->warn('⚠️  Failed to extract data from website. Using sample data instead.');
            $data = $this->getSampleData();
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Import categories
            $this->info('📂 Importing categories...');
            $categoryMap = $this->importCategories($data['categories'], $companyId);
            
            // Import products
            $this->info('📦 Importing products...');
            $this->importProducts($data['products'], $categoryMap, $companyId, $extractor, $skipImages);
            
            DB::commit();
            
            $this->info('✅ Successfully imported data!');
            $this->table(['Type', 'Count'], [
                ['Categories', count($data['categories'])],
                ['Products', count($data['products'])]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error importing data: ' . $e->getMessage());
            Log::error('Crackers data import failed: ' . $e->getMessage());
        }
    }
    
    private function importCategories($categories, $companyId)
    {
        $categoryMap = [];
        $progressBar = $this->output->createProgressBar(count($categories));
        
        foreach ($categories as $categoryData) {
            $category = Category::updateOrCreate(
                [
                    'slug' => $categoryData['slug'],
                    'company_id' => $companyId
                ],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'is_active' => $categoryData['is_active'],
                    'sort_order' => $categoryData['sort_order'],
                    'company_id' => $companyId
                ]
            );
            
            $categoryMap[$categoryData['name']] = $category->id;
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        return $categoryMap;
    }
    
    private function importProducts($products, $categoryMap, $companyId, $extractor, $skipImages)
    {
        $progressBar = $this->output->createProgressBar(count($products));
        
        foreach ($products as $productData) {
            // Get category ID
            $categoryId = array_values($categoryMap)[0] ?? 1; // Default to first category
            if (isset($productData['category_name']) && isset($categoryMap[$productData['category_name']])) {
                $categoryId = $categoryMap[$productData['category_name']];
            } elseif (isset($productData['category_id']) && isset($categoryMap[$productData['category_id']])) {
                $categoryId = $categoryMap[$productData['category_id']];
            }
            
            // Download and save image if available
            $featuredImage = null;
            if (!$skipImages && isset($productData['featured_image']) && !empty($productData['featured_image'])) {
                $imagePath = $extractor->downloadImage(
                    $productData['featured_image'],
                    $productData['slug']
                );
                if ($imagePath) {
                    $featuredImage = $imagePath;
                }
            }
            
            // Create or update product
            $product = Product::updateOrCreate(
                [
                    'slug' => $productData['slug'],
                    'company_id' => $companyId
                ],
                [
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'short_description' => $productData['short_description'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'category_id' => $categoryId,
                    'featured_image' => $featuredImage,
                    'is_active' => $productData['is_active'],
                    'is_featured' => $productData['is_featured'] ?? false,
                    'weight' => $productData['weight'] ?? 0,
                    'weight_unit' => $productData['weight_unit'] ?? 'kg',
                    'sku' => $productData['sku'],
                    'sort_order' => $productData['sort_order'] ?? 1,
                    'company_id' => $companyId
                ]
            );
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
    }
    
    private function getSampleData()
    {
        return [
            'categories' => [
                [
                    'name' => 'Sound Crackers',
                    'slug' => 'sound-crackers',
                    'description' => 'High quality sound crackers for celebrations',
                    'is_active' => true,
                    'sort_order' => 1
                ],
                [
                    'name' => 'Sparklers',
                    'slug' => 'sparklers',
                    'description' => 'Beautiful sparklers for kids and adults',
                    'is_active' => true,
                    'sort_order' => 2
                ],
                [
                    'name' => 'Flower Pots',
                    'slug' => 'flower-pots',
                    'description' => 'Colorful flower pot crackers',
                    'is_active' => true,
                    'sort_order' => 3
                ],
                [
                    'name' => 'Ground Chakkars',
                    'slug' => 'ground-chakkars',
                    'description' => 'Spinning ground chakkars',
                    'is_active' => true,
                    'sort_order' => 4
                ],
                [
                    'name' => 'Rockets',
                    'slug' => 'rockets',
                    'description' => 'Sky rockets and aerial fireworks',
                    'is_active' => true,
                    'sort_order' => 5
                ],
                [
                    'name' => 'Fountains',
                    'slug' => 'fountains',
                    'description' => 'Fountain crackers with colorful displays',
                    'is_active' => true,
                    'sort_order' => 6
                ],
                [
                    'name' => 'Fancy Crackers',
                    'slug' => 'fancy-crackers',
                    'description' => 'Fancy and novelty crackers',
                    'is_active' => true,
                    'sort_order' => 7
                ],
                [
                    'name' => 'Gift Boxes',
                    'slug' => 'gift-boxes',
                    'description' => 'Assorted crackers gift boxes',
                    'is_active' => true,
                    'sort_order' => 8
                ]
            ],
            'products' => [
                [
                    'name' => '10000 Wala',
                    'slug' => '10000-wala',
                    'description' => '10000 Wala - High quality sound crackers from Sivakasi manufactured with premium materials for loud sound effects',
                    'short_description' => 'High quality 10000 Wala crackers',
                    'price' => 299,
                    'stock' => 50,
                    'is_active' => true,
                    'is_featured' => true,
                    'category_name' => 'Sound Crackers',
                    'weight' => 1.5,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-10000W',
                    'sort_order' => 1
                ],
                [
                    'name' => '5000 Wala',
                    'slug' => '5000-wala',
                    'description' => '5000 Wala - High quality sound crackers from Sivakasi manufactured with premium materials',
                    'short_description' => 'High quality 5000 Wala crackers',
                    'price' => 199,
                    'stock' => 75,
                    'is_active' => true,
                    'is_featured' => true,
                    'category_name' => 'Sound Crackers',
                    'weight' => 1.0,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-5000W',
                    'sort_order' => 2
                ],
                [
                    'name' => '2000 Wala',
                    'slug' => '2000-wala',
                    'description' => '2000 Wala - High quality sound crackers from Sivakasi manufactured with premium materials',
                    'short_description' => 'High quality 2000 Wala crackers',
                    'price' => 99,
                    'stock' => 100,
                    'is_active' => true,
                    'is_featured' => false,
                    'category_name' => 'Sound Crackers',
                    'weight' => 0.5,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-2000W',
                    'sort_order' => 3
                ],
                [
                    'name' => 'Electric Sparklers',
                    'slug' => 'electric-sparklers',
                    'description' => 'Electric Sparklers - Beautiful sparklers for celebrations, safe for kids and adults',
                    'short_description' => 'Electric sparklers for kids and adults',
                    'price' => 49,
                    'stock' => 200,
                    'is_active' => true,
                    'is_featured' => true,
                    'category_name' => 'Sparklers',
                    'weight' => 0.2,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-ESPARK',
                    'sort_order' => 4
                ],
                [
                    'name' => 'Gold Sparklers',
                    'slug' => 'gold-sparklers',
                    'description' => 'Gold Sparklers - Premium quality sparklers with golden sparks',
                    'short_description' => 'Premium gold sparklers',
                    'price' => 39,
                    'stock' => 150,
                    'is_active' => true,
                    'is_featured' => false,
                    'category_name' => 'Sparklers',
                    'weight' => 0.2,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-GSPARK',
                    'sort_order' => 5
                ],
                [
                    'name' => 'Color Sparklers',
                    'slug' => 'color-sparklers',
                    'description' => 'Color Sparklers - Multi-colored sparklers for vibrant celebrations',
                    'short_description' => 'Multi-colored sparklers',
                    'price' => 59,
                    'stock' => 120,
                    'is_active' => true,
                    'is_featured' => true,
                    'category_name' => 'Sparklers',
                    'weight' => 0.2,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-CSPARK',
                    'sort_order' => 6
                ],
                [
                    'name' => 'Big Flower Pot',
                    'slug' => 'big-flower-pot',
                    'description' => 'Big Flower Pot - Large flower pot crackers with colorful display',
                    'short_description' => 'Large flower pot crackers',
                    'price' => 25,
                    'stock' => 80,
                    'is_active' => true,
                    'is_featured' => false,
                    'category_name' => 'Flower Pots',
                    'weight' => 0.3,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-BFPOT',
                    'sort_order' => 7
                ],
                [
                    'name' => 'Small Flower Pot',
                    'slug' => 'small-flower-pot',
                    'description' => 'Small Flower Pot - Compact flower pot crackers perfect for small celebrations',
                    'short_description' => 'Compact flower pot crackers',
                    'price' => 15,
                    'stock' => 120,
                    'is_active' => true,
                    'is_featured' => false,
                    'category_name' => 'Flower Pots',
                    'weight' => 0.1,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-SFPOT',
                    'sort_order' => 8
                ],
                [
                    'name' => 'Special Flower Pot',
                    'slug' => 'special-flower-pot',
                    'description' => 'Special Flower Pot - Premium flower pot crackers with special effects',
                    'short_description' => 'Premium flower pot crackers',
                    'price' => 35,
                    'stock' => 60,
                    'is_active' => true,
                    'is_featured' => true,
                    'category_name' => 'Flower Pots',
                    'weight' => 0.4,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-SPFPOT',
                    'sort_order' => 9
                ],
                [
                    'name' => 'Ground Chakkar Big',
                    'slug' => 'ground-chakkar-big',
                    'description' => 'Ground Chakkar Big - Large spinning ground chakkar with bright colors',
                    'short_description' => 'Large spinning ground chakkar',
                    'price' => 45,
                    'stock' => 70,
                    'is_active' => true,
                    'is_featured' => false,
                    'category_name' => 'Ground Chakkars',
                    'weight' => 0.5,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-GCBIG',
                    'sort_order' => 10
                ],
                [
                    'name' => 'Ground Chakkar Small',
                    'slug' => 'ground-chakkar-small',
                    'description' => 'Ground Chakkar Small - Compact spinning ground chakkar',
                    'short_description' => 'Compact spinning ground chakkar',
                    'price' => 25,
                    'stock' => 100,
                    'is_active' => true,
                    'is_featured' => false,
                    'category_name' => 'Ground Chakkars',
                    'weight' => 0.2,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-GCSML',
                    'sort_order' => 11
                ],
                [
                    'name' => 'Whistling Rocket',
                    'slug' => 'whistling-rocket',
                    'description' => 'Whistling Rocket - High flying rocket with whistling sound effect',
                    'short_description' => 'High flying whistling rocket',
                    'price' => 75,
                    'stock' => 40,
                    'is_active' => true,
                    'is_featured' => true,
                    'category_name' => 'Rockets',
                    'weight' => 0.6,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-WROCKET',
                    'sort_order' => 12
                ],
                [
                    'name' => 'Baby Rocket',
                    'slug' => 'baby-rocket',
                    'description' => 'Baby Rocket - Small rocket perfect for beginners',
                    'short_description' => 'Small rocket for beginners',
                    'price' => 35,
                    'stock' => 80,
                    'is_active' => true,
                    'is_featured' => false,
                    'category_name' => 'Rockets',
                    'weight' => 0.3,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-BROCKET',
                    'sort_order' => 13
                ],
                [
                    'name' => 'Color Fountain',
                    'slug' => 'color-fountain',
                    'description' => 'Color Fountain - Multi-colored fountain with spectacular display',
                    'short_description' => 'Multi-colored fountain display',
                    'price' => 85,
                    'stock' => 50,
                    'is_active' => true,
                    'is_featured' => true,
                    'category_name' => 'Fountains',
                    'weight' => 0.8,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-CFOUNT',
                    'sort_order' => 14
                ],
                [
                    'name' => 'Gold Fountain',
                    'slug' => 'gold-fountain',
                    'description' => 'Gold Fountain - Golden fountain with elegant display',
                    'short_description' => 'Golden fountain display',
                    'price' => 65,
                    'stock' => 60,
                    'is_active' => true,
                    'is_featured' => false,
                    'category_name' => 'Fountains',
                    'weight' => 0.6,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-GFOUNT',
                    'sort_order' => 15
                ],
                [
                    'name' => 'Fancy Butterfly',
                    'slug' => 'fancy-butterfly',
                    'description' => 'Fancy Butterfly - Novelty butterfly shaped crackers',
                    'short_description' => 'Novelty butterfly crackers',
                    'price' => 55,
                    'stock' => 45,
                    'is_active' => true,
                    'is_featured' => true,
                    'category_name' => 'Fancy Crackers',
                    'weight' => 0.4,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-FBFLY',
                    'sort_order' => 16
                ],
                [
                    'name' => 'Magic Stick',
                    'slug' => 'magic-stick',
                    'description' => 'Magic Stick - Magical stick crackers with special effects',
                    'short_description' => 'Magical stick crackers',
                    'price' => 29,
                    'stock' => 90,
                    'is_active' => true,
                    'is_featured' => false,
                    'category_name' => 'Fancy Crackers',
                    'weight' => 0.2,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-MSTICK',
                    'sort_order' => 17
                ],
                [
                    'name' => 'Diwali Gift Box',
                    'slug' => 'diwali-gift-box',
                    'description' => 'Diwali Gift Box - Complete assorted crackers gift box for Diwali celebrations',
                    'short_description' => 'Complete Diwali crackers gift box',
                    'price' => 499,
                    'stock' => 25,
                    'is_active' => true,
                    'is_featured' => true,
                    'category_name' => 'Gift Boxes',
                    'weight' => 3.0,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-DGIFTBOX',
                    'sort_order' => 18
                ],
                [
                    'name' => 'Family Pack',
                    'slug' => 'family-pack',
                    'description' => 'Family Pack - Perfect family pack with variety of crackers',
                    'short_description' => 'Perfect family crackers pack',
                    'price' => 299,
                    'stock' => 35,
                    'is_active' => true,
                    'is_featured' => true,
                    'category_name' => 'Gift Boxes',
                    'weight' => 2.0,
                    'weight_unit' => 'kg',
                    'sku' => 'CR-FPACK',
                    'sort_order' => 19
                ]
            ]
        ];
    }
}
