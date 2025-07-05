<?php

namespace App\Services\Import;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SuperAdmin\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class CrackersDataImporter
{
    protected $baseUrl = 'https://thiruchendurmurugancrackers.com/';
    protected $company;
    protected $categories = [];
    protected $products = [];

    public function __construct(Company $company = null)
    {
        $this->company = $company;
    }

    /**
     * Import data from HTML content
     */
    public function importFromHtml(string $htmlContent): array
    {
        try {
            // Parse HTML and extract data
            $this->parseHtmlContent($htmlContent);
            
            // Create categories
            $categoriesCreated = $this->createCategories();
            
            // Create products
            $productsCreated = $this->createProducts();
            
            return [
                'success' => true,
                'categories_created' => $categoriesCreated,
                'products_created' => $productsCreated,
                'total_categories' => count($this->categories),
                'total_products' => count($this->products)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error importing crackers data: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Parse HTML content and extract categories and products
     */
    public function parseHtmlContent(string $htmlContent): void
    {
        // Create DOM document
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        
        // Extract categories
        $categoryButtons = $xpath->query('//button[contains(@class, "items-center")]//span[2]');
        foreach ($categoryButtons as $button) {
            $categoryName = trim($button->textContent);
            if (!empty($categoryName)) {
                $this->categories[] = [
                    'name' => $categoryName,
                    'slug' => Str::slug($categoryName),
                    'description' => 'Imported from crackers website',
                    'is_active' => true
                ];
            }
        }

        // Extract products from desktop view
        $this->extractDesktopProducts($xpath);
        
        // Extract products from mobile view
        $this->extractMobileProducts($xpath);
        
        // Remove duplicates
        $this->products = array_unique($this->products, SORT_REGULAR);
    }

    /**
     * Extract products from desktop view
     */
    protected function extractDesktopProducts(DOMXPath $xpath): void
    {
        $productRows = $xpath->query('//div[contains(@class, "hidden md:flex items-center")]');
        
        foreach ($productRows as $row) {
            $product = $this->extractProductFromRow($row, $xpath);
            if ($product) {
                $this->products[] = $product;
            }
        }
    }

    /**
     * Extract products from mobile view
     */
    protected function extractMobileProducts(DOMXPath $xpath): void
    {
        $mobileCards = $xpath->query('//div[contains(@class, "grid-cols-1 md:hidden")]');
        
        foreach ($mobileCards as $card) {
            $product = $this->extractProductFromMobileCard($card, $xpath);
            if ($product) {
                $this->products[] = $product;
            }
        }
    }

    /**
     * Extract product data from desktop row
     */
    protected function extractProductFromRow($row, DOMXPath $xpath): ?array
    {
        try {
            // Get product code
            $codeElement = $xpath->query('.//div[contains(@class, "w-1/12")]', $row)->item(0);
            $code = $codeElement ? trim($codeElement->textContent) : '';

            // Get product image
            $imageElement = $xpath->query('.//img', $row)->item(0);
            $imageSrc = $imageElement ? $imageElement->getAttribute('src') : '';

            // Get product name
            $nameElement = $xpath->query('.//div[contains(@class, "w-3/12")]', $row)->item(0);
            $name = $nameElement ? trim($nameElement->textContent) : '';

            // Get prices
            $priceContainer = $xpath->query('.//div[contains(@class, "w-2/12")]/div', $row);
            $originalPrice = 0;
            $salePrice = 0;
            
            if ($priceContainer->length >= 2) {
                $originalPriceText = $priceContainer->item(0)->textContent;
                $salePriceText = $priceContainer->item(1)->textContent;
                
                $originalPrice = $this->extractPrice($originalPriceText);
                $salePrice = $this->extractPrice($salePriceText);
            }

            if (empty($name) || empty($code)) {
                return null;
            }

            return [
                'code' => $code,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "High-quality crackers - {$name}",
                'short_description' => $name,
                'sku' => 'CRK-' . str_pad($code, 4, '0', STR_PAD_LEFT),
                'price' => $originalPrice,
                'sale_price' => $salePrice,
                'stock_quantity' => 100,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => $name,
                'meta_description' => "Buy {$name} crackers online",
                'weight' => 0.5,
                'dimensions' => json_encode(['length' => 10, 'width' => 5, 'height' => 3]),
                'image_url' => $this->processImageUrl($imageSrc),
                'category_name' => $this->getCurrentCategoryName($row)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error extracting product from row: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract product data from mobile card
     */
    protected function extractProductFromMobileCard($card, DOMXPath $xpath): ?array
    {
        try {
            // Get product code
            $codeElement = $xpath->query('.//div[contains(@class, "h-6 w-8")]', $card)->item(0);
            $code = $codeElement ? trim($codeElement->textContent) : '';

            // Get product image
            $imageElement = $xpath->query('.//img', $card)->item(0);
            $imageSrc = $imageElement ? $imageElement->getAttribute('src') : '';

            // Get product name
            $nameElement = $xpath->query('.//div[contains(@class, "capitalize")]', $card)->item(0);
            $name = $nameElement ? trim($nameElement->textContent) : '';

            // Get prices
            $priceElements = $xpath->query('.//div[contains(@class, "line-through")]', $card);
            $salePriceElements = $xpath->query('.//div[contains(@class, "text-custom-orange")]', $card);
            
            $originalPrice = 0;
            $salePrice = 0;
            
            if ($priceElements->length > 0) {
                $originalPrice = $this->extractPrice($priceElements->item(0)->textContent);
            }
            
            if ($salePriceElements->length > 0) {
                $salePrice = $this->extractPrice($salePriceElements->item(0)->textContent);
            }

            if (empty($name) || empty($code)) {
                return null;
            }

            return [
                'code' => $code,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "Premium quality crackers - {$name}",
                'short_description' => $name,
                'sku' => 'CRK-' . str_pad($code, 4, '0', STR_PAD_LEFT),
                'price' => $originalPrice,
                'sale_price' => $salePrice,
                'stock_quantity' => 100,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => $name,
                'meta_description' => "Buy {$name} crackers online",
                'weight' => 0.5,
                'dimensions' => json_encode(['length' => 10, 'width' => 5, 'height' => 3]),
                'image_url' => $this->processImageUrl($imageSrc),
                'category_name' => 'General Crackers'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error extracting product from mobile card: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract price from text
     */
    protected function extractPrice(string $priceText): float
    {
        preg_match('/[\d,]+(?:\.\d{2})?/', $priceText, $matches);
        if (!empty($matches)) {
            return (float) str_replace(',', '', $matches[0]);
        }
        return 0;
    }

    /**
     * Process image URL
     */
    protected function processImageUrl(string $imageSrc): string
    {
        if (empty($imageSrc)) {
            return '';
        }
        
        // Remove leading slash if present
        $imageSrc = ltrim($imageSrc, '/');
        
        // Build full URL
        return $this->baseUrl . $imageSrc;
    }

    /**
     * Get current category name (simplified version)
     */
    protected function getCurrentCategoryName($row): string
    {
        // This is a simplified version - in a real scenario, you'd need to track
        // which category section the product belongs to
        return 'Crackers';
    }

    /**
     * Create categories in database
     */
    protected function createCategories(): int
    {
        $created = 0;
        
        foreach ($this->categories as $categoryData) {
            $category = Category::where('slug', $categoryData['slug'])->first();
            
            if (!$category) {
                $categoryData['company_id'] = $this->company ? $this->company->id : null;
                $category = Category::create($categoryData);
                $created++;
            }
        }
        
        return $created;
    }

    /**
     * Create products in database
     */
    protected function createProducts(): int
    {
        $created = 0;
        
        foreach ($this->products as $productData) {
            // Check if product already exists
            $existingProduct = Product::where('sku', $productData['sku'])->first();
            
            if (!$existingProduct) {
                // Find or create category
                $category = Category::where('name', 'LIKE', '%' . $productData['category_name'] . '%')->first();
                if (!$category) {
                    $category = Category::create([
                        'name' => $productData['category_name'],
                        'slug' => Str::slug($productData['category_name']),
                        'description' => 'Auto-created category',
                        'is_active' => true,
                        'company_id' => $this->company ? $this->company->id : null
                    ]);
                }
                
                // Remove category_name and image_url from product data
                $imageUrl = $productData['image_url'];
                unset($productData['category_name'], $productData['image_url']);
                
                // Set company_id if available
                if ($this->company) {
                    $productData['company_id'] = $this->company->id;
                }
                
                // Set category_id
                $productData['category_id'] = $category->id;
                
                // Create product
                $product = Product::create($productData);
                
                // Handle product image
                if (!empty($imageUrl)) {
                    $this->createProductImage($product, $imageUrl);
                }
                
                $created++;
            }
        }
        
        return $created;
    }

    /**
     * Create product image
     */
    protected function createProductImage(Product $product, string $imageUrl): void
    {
        try {
            // Download image
            $response = Http::timeout(30)->get($imageUrl);
            
            if ($response->successful()) {
                $imageContent = $response->body();
                $extension = $this->getImageExtension($imageUrl);
                $filename = 'products/' . $product->slug . '-' . time() . '.' . $extension;
                
                // Store image
                Storage::disk('public')->put($filename, $imageContent);
                
                // Create product image record
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $filename,
                    'alt_text' => $product->name,
                    'is_primary' => true,
                    'sort_order' => 1
                ]);
                
                Log::info("Image downloaded for product: {$product->name}");
            } else {
                Log::warning("Failed to download image for product: {$product->name}");
            }
            
        } catch (\Exception $e) {
            Log::error("Error downloading image for product {$product->name}: " . $e->getMessage());
        }
    }

    /**
     * Get image extension from URL
     */
    protected function getImageExtension(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        // Default to jpg if no extension found
        return $extension ?: 'jpg';
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_categories' => count($this->categories),
            'total_products' => count($this->products),
            'categories' => $this->categories,
            'products' => array_slice($this->products, 0, 10) // First 10 products as sample
        ];
    }
}
