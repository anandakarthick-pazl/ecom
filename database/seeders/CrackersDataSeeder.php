<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Services\CrackersDataExtractor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CrackersDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting crackers data import...');
        
        // Get the current company ID (assuming single tenant for now)
        $companyId = $this->getCompanyId();
        
        if (!$companyId) {
            $this->command->error('No company found. Please create a company first.');
            return;
        }

        // Initialize the data extractor
        $extractor = new CrackersDataExtractor();
        
        // Extract data from website
        $this->command->info('Extracting data from website...');
        $data = $extractor->extractData();
        
        if (!$data) {
            $this->command->error('Failed to extract data from website. Using sample data instead.');
            $data = $this->getSampleData();
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Import categories
            $this->command->info('Importing categories...');
            $categoryMap = $this->importCategories($data['categories'], $companyId);
            
            // Import products
            $this->command->info('Importing products...');
            $this->importProducts($data['products'], $categoryMap, $companyId, $extractor);
            
            DB::commit();
            
            $this->command->info('Successfully imported ' . count($data['categories']) . ' categories and ' . count($data['products']) . ' products.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error importing data: ' . $e->getMessage());
            Log::error('Crackers data import failed: ' . $e->getMessage());
        }
    }
    
    private function getCompanyId()
    {
        // Try to get company ID from different sources
        $companyId = null;
        
        // Check if there's a default company
        if (class_exists('App\Models\Company')) {
            $company = DB::table('companies')->first();
            if ($company) {
                $companyId = $company->id;
            }
        }
        
        // If no company found, create a default one for single tenant
        if (!$companyId) {
            $companyId = 1; // Default company ID
        }
        
        return $companyId;
    }
    
    private function importCategories($categories, $companyId)
    {
        $categoryMap = [];
        
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
            
            $this->command->info('  - Imported category: ' . $categoryData['name']);
        }
        
        return $categoryMap;
    }
    
    private function importProducts($products, $categoryMap, $companyId, $extractor)
    {
        foreach ($products as $productData) {
            // Get category ID
            $categoryId = 1; // Default category
            if (isset($productData['category_name']) && isset($categoryMap[$productData['category_name']])) {
                $categoryId = $categoryMap[$productData['category_name']];
            } elseif (isset($productData['category_id'])) {
                $categoryId = $productData['category_id'];
            }
            
            // Download and save image if available
            $featuredImage = null;
            if (isset($productData['featured_image']) && !empty($productData['featured_image'])) {
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
            
            $this->command->info('  - Imported product: ' . $productData['name']);
        }
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
                    'description' => '10000 Wala - High quality sound crackers from Sivakasi',
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
                    'description' => '5000 Wala - High quality sound crackers from Sivakasi',
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
                    'description' => '2000 Wala - High quality sound crackers from Sivakasi',
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
                    'description' => 'Electric Sparklers - Beautiful sparklers for celebrations',
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
                    'description' => 'Gold Sparklers - Premium quality sparklers',
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
                ]
            ]
        ];
    }
}
