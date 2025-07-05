<?php
/**
 * Standalone Web Scraper for Crackers Website
 * This script can be used to extract data from the website manually
 */

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CrackersScraper
{
    private $client;
    private $baseUrl = 'https://thiruchendurmurugancrackers.com/';
    
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);
    }
    
    public function scrapeData()
    {
        echo "🎆 Starting data extraction from {$this->baseUrl}...\n";
        
        try {
            // Fetch main page
            $response = $this->client->get($this->baseUrl . 'order-now');
            $html = $response->getBody()->getContents();
            
            if (empty($html)) {
                echo "❌ Failed to fetch page content\n";
                return null;
            }
            
            echo "✅ Successfully fetched page content\n";
            
            // Parse HTML
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
            
            // Extract data
            $data = [
                'categories' => $this->extractCategories($xpath),
                'products' => $this->extractProducts($xpath),
                'images' => $this->extractImages($xpath)
            ];
            
            // Save to JSON file
            $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents('crackers_data.json', $jsonData);
            
            echo "💾 Data saved to crackers_data.json\n";
            
            return $data;
            
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            return null;
        }
    }
    
    private function extractCategories($xpath)
    {
        $categories = [];
        
        // Try different selectors for categories
        $selectors = [
            '//nav//a[contains(@class, "category")]',
            '//ul[contains(@class, "categories")]//a',
            '//div[contains(@class, "category")]//a',
            '//a[contains(@href, "category")]',
            '//h2[contains(text(), "Categories")]/..//a',
            '//div[@class="menu"]//a'
        ];
        
        foreach ($selectors as $selector) {
            $nodes = $xpath->query($selector);
            if ($nodes->length > 0) {
                echo "📂 Found categories using selector: {$selector}\n";
                foreach ($nodes as $node) {
                    $name = trim($node->textContent);
                    $href = $node->getAttribute('href');
                    
                    if (!empty($name) && strlen($name) > 2) {
                        $categories[] = [
                            'name' => $name,
                            'url' => $href,
                            'slug' => $this->createSlug($name)
                        ];
                    }
                }
                break;
            }
        }
        
        echo "📂 Extracted " . count($categories) . " categories\n";
        return $categories;
    }
    
    private function extractProducts($xpath)
    {
        $products = [];
        
        // Try different selectors for products
        $selectors = [
            '//div[contains(@class, "product")]',
            '//div[contains(@class, "item")]',
            '//li[contains(@class, "product")]',
            '//article[contains(@class, "product")]',
            '//div[@class="product-card"]',
            '//div[@class="item-card"]'
        ];
        
        foreach ($selectors as $selector) {
            $nodes = $xpath->query($selector);
            if ($nodes->length > 0) {
                echo "📦 Found products using selector: {$selector}\n";
                foreach ($nodes as $node) {
                    $product = $this->extractProductData($node, $xpath);
                    if ($product) {
                        $products[] = $product;
                    }
                }
                break;
            }
        }
        
        echo "📦 Extracted " . count($products) . " products\n";
        return $products;
    }
    
    private function extractProductData($node, $xpath)
    {
        // Extract product name
        $nameNode = $xpath->query('.//h1 | .//h2 | .//h3 | .//h4 | .//h5 | .//h6', $node)->item(0);
        $name = $nameNode ? trim($nameNode->textContent) : null;
        
        // Extract price
        $priceNode = $xpath->query('.//*[contains(@class, "price")] | .//*[contains(text(), "₹")] | .//*[contains(text(), "Rs")]', $node)->item(0);
        $price = $priceNode ? $this->extractPrice($priceNode->textContent) : null;
        
        // Extract image
        $imageNode = $xpath->query('.//img', $node)->item(0);
        $image = $imageNode ? $imageNode->getAttribute('src') : null;
        
        // Extract description
        $descNode = $xpath->query('.//p | .//*[contains(@class, "description")]', $node)->item(0);
        $description = $descNode ? trim($descNode->textContent) : null;
        
        if (!$name) {
            return null;
        }
        
        return [
            'name' => $name,
            'slug' => $this->createSlug($name),
            'price' => $price,
            'image' => $image ? $this->normalizeUrl($image) : null,
            'description' => $description
        ];
    }
    
    private function extractImages($xpath)
    {
        $images = [];
        
        $imageNodes = $xpath->query('//img');
        foreach ($imageNodes as $node) {
            $src = $node->getAttribute('src');
            $alt = $node->getAttribute('alt');
            
            if (!empty($src)) {
                $images[] = [
                    'src' => $this->normalizeUrl($src),
                    'alt' => $alt
                ];
            }
        }
        
        echo "🖼️  Extracted " . count($images) . " images\n";
        return $images;
    }
    
    private function extractPrice($text)
    {
        preg_match('/[\d,]+\.?\d*/', $text, $matches);
        return isset($matches[0]) ? (float) str_replace(',', '', $matches[0]) : null;
    }
    
    private function createSlug($text)
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
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
    
    public function downloadImages($images, $downloadDir = 'downloads/images')
    {
        if (!is_dir($downloadDir)) {
            mkdir($downloadDir, 0755, true);
        }
        
        echo "📥 Downloading images...\n";
        
        foreach ($images as $index => $image) {
            try {
                $response = $this->client->get($image['src']);
                $imageData = $response->getBody()->getContents();
                
                $filename = basename(parse_url($image['src'], PHP_URL_PATH));
                if (empty($filename)) {
                    $filename = "image_{$index}.jpg";
                }
                
                $filepath = $downloadDir . '/' . $filename;
                file_put_contents($filepath, $imageData);
                
                echo "  ✅ Downloaded: {$filename}\n";
                
            } catch (Exception $e) {
                echo "  ❌ Failed to download: {$image['src']}\n";
            }
        }
    }
}

// Run the scraper
echo "🎯 Crackers Data Scraper\n";
echo "========================\n\n";

$scraper = new CrackersScraper();
$data = $scraper->scrapeData();

if ($data) {
    echo "\n📊 Summary:\n";
    echo "- Categories: " . count($data['categories']) . "\n";
    echo "- Products: " . count($data['products']) . "\n";
    echo "- Images: " . count($data['images']) . "\n";
    
    // Ask if user wants to download images
    if (!empty($data['images'])) {
        echo "\n📥 Download images? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $input = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($input) === 'y') {
            $scraper->downloadImages($data['images']);
        }
    }
    
    echo "\n✅ Scraping completed successfully!\n";
} else {
    echo "\n❌ Scraping failed!\n";
}

echo "\n🎉 Done!\n";
