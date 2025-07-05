<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CrackersDataExtractor
{
    private $client;
    private $baseUrl;
    private $categories = [];
    private $products = [];

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);
        $this->baseUrl = 'https://thiruchendurmurugancrackers.com/';
    }

    public function extractData()
    {
        try {
            // First, get the main page
            $mainPageContent = $this->fetchPage($this->baseUrl . 'order-now');
            
            if (!$mainPageContent) {
                Log::error('Failed to fetch main page content');
                return false;
            }

            // Parse the HTML content
            $dom = new \DOMDocument();
            @$dom->loadHTML($mainPageContent);
            $xpath = new \DOMXPath($dom);

            // Extract categories and products
            $this->extractCategories($xpath);
            $this->extractProducts($xpath);

            return [
                'categories' => $this->categories,
                'products' => $this->products
            ];
        } catch (\Exception $e) {
            Log::error('Error extracting data: ' . $e->getMessage());
            return false;
        }
    }

    private function fetchPage($url)
    {
        try {
            $response = $this->client->get($url);
            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            Log::error('Failed to fetch page: ' . $url . ' - ' . $e->getMessage());
            return null;
        }
    }

    private function extractCategories($xpath)
    {
        // Look for common category selectors
        $categorySelectors = [
            '//nav//a[contains(@class, "category")]',
            '//ul[contains(@class, "category")]//a',
            '//div[contains(@class, "category")]//a',
            '//li[contains(@class, "category")]//a',
            '//a[contains(@href, "category")]'
        ];

        foreach ($categorySelectors as $selector) {
            $categoryNodes = $xpath->query($selector);
            if ($categoryNodes->length > 0) {
                foreach ($categoryNodes as $node) {
                    $categoryName = trim($node->textContent);
                    $categoryUrl = $node->getAttribute('href');
                    
                    if (!empty($categoryName) && !empty($categoryUrl)) {
                        $this->categories[] = [
                            'name' => $categoryName,
                            'slug' => Str::slug($categoryName),
                            'url' => $this->normalizeUrl($categoryUrl),
                            'description' => $categoryName . ' crackers and fireworks',
                            'is_active' => true,
                            'sort_order' => count($this->categories) + 1
                        ];
                    }
                }
                break; // Found categories, stop looking
            }
        }

        // If no categories found, create default ones
        if (empty($this->categories)) {
            $this->createDefaultCategories();
        }
    }

    private function extractProducts($xpath)
    {
        // Look for common product selectors
        $productSelectors = [
            '//div[contains(@class, "product")]',
            '//div[contains(@class, "item")]',
            '//div[contains(@class, "card")]',
            '//li[contains(@class, "product")]',
            '//article[contains(@class, "product")]'
        ];

        foreach ($productSelectors as $selector) {
            $productNodes = $xpath->query($selector);
            if ($productNodes->length > 0) {
                foreach ($productNodes as $node) {
                    $product = $this->extractProductFromNode($node, $xpath);
                    if ($product) {
                        $this->products[] = $product;
                    }
                }
                break; // Found products, stop looking
            }
        }

        // If no products found, create sample products
        if (empty($this->products)) {
            $this->createSampleProducts();
        }
    }

    private function extractProductFromNode($node, $xpath)
    {
        try {
            // Extract product name
            $nameNode = $xpath->query('.//h1 | .//h2 | .//h3 | .//h4 | .//h5 | .//h6 | .//*[contains(@class, "title")] | .//*[contains(@class, "name")]', $node)->item(0);
            $name = $nameNode ? trim($nameNode->textContent) : 'Unknown Product';

            // Extract price
            $priceNode = $xpath->query('.//*[contains(@class, "price")] | .//*[contains(text(), "₹")] | .//*[contains(text(), "Rs")]', $node)->item(0);
            $price = $priceNode ? $this->extractPrice($priceNode->textContent) : 0;

            // Extract image
            $imageNode = $xpath->query('.//img', $node)->item(0);
            $imageUrl = $imageNode ? $imageNode->getAttribute('src') : null;

            // Extract description
            $descNode = $xpath->query('.//*[contains(@class, "description")] | .//p', $node)->item(0);
            $description = $descNode ? trim($descNode->textContent) : $name . ' - High quality crackers from Sivakasi';

            if (empty($name) || $name === 'Unknown Product') {
                return null;
            }

            return [
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'short_description' => Str::limit($description, 100),
                'price' => $price,
                'stock' => rand(10, 100),
                'featured_image' => $imageUrl ? $this->normalizeUrl($imageUrl) : null,
                'is_active' => true,
                'is_featured' => rand(0, 1),
                'category_id' => 1, // Will be updated later
                'weight' => rand(1, 10) / 10,
                'weight_unit' => 'kg',
                'sku' => 'CR-' . Str::random(6),
                'sort_order' => count($this->products) + 1
            ];
        } catch (\Exception $e) {
            Log::error('Error extracting product from node: ' . $e->getMessage());
            return null;
        }
    }

    private function extractPrice($priceText)
    {
        // Extract numeric price from text
        preg_match('/[\d,]+\.?\d*/', $priceText, $matches);
        return isset($matches[0]) ? (float) str_replace(',', '', $matches[0]) : 0;
    }

    private function normalizeUrl($url)
    {
        if (strpos($url, 'http') === 0) {
            return $url;
        }
        
        if (strpos($url, '/') === 0) {
            return rtrim($this->baseUrl, '/') . $url;
        }
        
        return rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/');
    }

    private function createDefaultCategories()
    {
        $defaultCategories = [
            ['name' => 'Sound Crackers', 'description' => 'High quality sound crackers for celebrations'],
            ['name' => 'Sparklers', 'description' => 'Beautiful sparklers for kids and adults'],
            ['name' => 'Flower Pots', 'description' => 'Colorful flower pot crackers'],
            ['name' => 'Ground Chakkars', 'description' => 'Spinning ground chakkars'],
            ['name' => 'Rockets', 'description' => 'Sky rockets and aerial fireworks'],
            ['name' => 'Fountains', 'description' => 'Fountain crackers with colorful displays'],
            ['name' => 'Fancy Crackers', 'description' => 'Fancy and novelty crackers'],
            ['name' => 'Gift Boxes', 'description' => 'Assorted crackers gift boxes'],
            ['name' => 'Twinkling Stars', 'description' => 'Twinkling star crackers'],
            ['name' => 'Bombs', 'description' => 'Atom bombs and sound bombs']
        ];

        foreach ($defaultCategories as $index => $category) {
            $this->categories[] = [
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'is_active' => true,
                'sort_order' => $index + 1
            ];
        }
    }

    private function createSampleProducts()
    {
        $sampleProducts = [
            ['name' => '10000 Wala', 'price' => 299, 'category' => 'Sound Crackers'],
            ['name' => '5000 Wala', 'price' => 199, 'category' => 'Sound Crackers'],
            ['name' => '2000 Wala', 'price' => 99, 'category' => 'Sound Crackers'],
            ['name' => 'Electric Sparklers', 'price' => 49, 'category' => 'Sparklers'],
            ['name' => 'Gold Sparklers', 'price' => 39, 'category' => 'Sparklers'],
            ['name' => 'Color Sparklers', 'price' => 59, 'category' => 'Sparklers'],
            ['name' => 'Big Flower Pot', 'price' => 25, 'category' => 'Flower Pots'],
            ['name' => 'Small Flower Pot', 'price' => 15, 'category' => 'Flower Pots'],
            ['name' => 'Special Flower Pot', 'price' => 35, 'category' => 'Flower Pots'],
            ['name' => 'Ground Chakkar Big', 'price' => 45, 'category' => 'Ground Chakkars'],
            ['name' => 'Ground Chakkar Small', 'price' => 25, 'category' => 'Ground Chakkars'],
            ['name' => 'Whistling Rocket', 'price' => 75, 'category' => 'Rockets'],
            ['name' => 'Baby Rocket', 'price' => 35, 'category' => 'Rockets'],
            ['name' => 'Color Fountain', 'price' => 85, 'category' => 'Fountains'],
            ['name' => 'Gold Fountain', 'price' => 65, 'category' => 'Fountains'],
            ['name' => 'Fancy Butterfly', 'price' => 55, 'category' => 'Fancy Crackers'],
            ['name' => 'Magic Stick', 'price' => 29, 'category' => 'Fancy Crackers'],
            ['name' => 'Diwali Gift Box', 'price' => 499, 'category' => 'Gift Boxes'],
            ['name' => 'Family Pack', 'price' => 299, 'category' => 'Gift Boxes'],
            ['name' => 'Twinkling Star Big', 'price' => 19, 'category' => 'Twinkling Stars'],
            ['name' => 'Twinkling Star Small', 'price' => 12, 'category' => 'Twinkling Stars'],
            ['name' => 'Atom Bomb', 'price' => 89, 'category' => 'Bombs'],
            ['name' => 'Hydogen Bomb', 'price' => 149, 'category' => 'Bombs']
        ];

        foreach ($sampleProducts as $index => $product) {
            $this->products[] = [
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $product['name'] . ' - High quality crackers from Sivakasi manufactured with premium materials',
                'short_description' => 'High quality ' . $product['name'] . ' crackers',
                'price' => $product['price'],
                'stock' => rand(20, 100),
                'is_active' => true,
                'is_featured' => rand(0, 1),
                'category_name' => $product['category'],
                'weight' => rand(1, 20) / 10,
                'weight_unit' => 'kg',
                'sku' => 'CR-' . strtoupper(Str::random(6)),
                'sort_order' => $index + 1
            ];
        }
    }

    public function downloadImage($imageUrl, $fileName)
    {
        try {
            if (!$imageUrl) {
                return null;
            }

            $response = $this->client->get($imageUrl);
            $imageContent = $response->getBody()->getContents();
            
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            if (!$extension) {
                $extension = 'jpg';
            }
            
            $fileName = $fileName . '.' . $extension;
            $path = 'products/' . $fileName;
            
            Storage::disk('public')->put($path, $imageContent);
            
            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to download image: ' . $imageUrl . ' - ' . $e->getMessage());
            return null;
        }
    }
}
