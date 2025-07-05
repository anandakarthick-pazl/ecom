<?php
/*
 * Theme Preview Image Generator
 * This script generates sample preview images for themes
 * Run this script to generate preview images for all themes
 */

// Theme configurations
$themes = [
    'vibrant-marketplace' => [
        'name' => 'Vibrant Marketplace',
        'primary' => '#FF6B6B',
        'secondary' => '#4ECDC4',
        'accent' => '#45B7D1'
    ],
    'neon-glow-store' => [
        'name' => 'Neon Glow Store',
        'primary' => '#00D4FF',
        'secondary' => '#FF0080',
        'accent' => '#39FF14'
    ],
    'pastel-dream' => [
        'name' => 'Pastel Dream',
        'primary' => '#FFB6C1',
        'secondary' => '#E0E6FF',
        'accent' => '#98FB98'
    ],
    'tropical-paradise' => [
        'name' => 'Tropical Paradise',
        'primary' => '#FF7F50',
        'secondary' => '#20B2AA',
        'accent' => '#FFD700'
    ],
    'retro-synthwave' => [
        'name' => 'Retro Synthwave',
        'primary' => '#FF0080',
        'secondary' => '#00FFFF',
        'accent' => '#FFFF00'
    ],
    'sunset-orange' => [
        'name' => 'Sunset Orange',
        'primary' => '#FF8C00',
        'secondary' => '#FF6347',
        'accent' => '#FFD700'
    ],
    'ocean-breeze' => [
        'name' => 'Ocean Breeze',
        'primary' => '#1E90FF',
        'secondary' => '#00CED1',
        'accent' => '#87CEEB'
    ],
    'galaxy-purple' => [
        'name' => 'Galaxy Purple',
        'primary' => '#8A2BE2',
        'secondary' => '#9932CC',
        'accent' => '#DA70D6'
    ],
    'forest-green' => [
        'name' => 'Forest Green',
        'primary' => '#228B22',
        'secondary' => '#32CD32',
        'accent' => '#90EE90'
    ],
    'electric-blue' => [
        'name' => 'Electric Blue',
        'primary' => '#0080FF',
        'secondary' => '#00BFFF',
        'accent' => '#87CEFA'
    ],
    'rose-gold-luxury' => [
        'name' => 'Rose Gold Luxury',
        'primary' => '#E8B4B8',
        'secondary' => '#D4A574',
        'accent' => '#F7E7CE'
    ],
    'mint-fresh' => [
        'name' => 'Mint Fresh',
        'primary' => '#98FB98',
        'secondary' => '#00FA9A',
        'accent' => '#AFEEEE'
    ]
];

// Function to convert hex to RGB
function hexToRgb($hex) {
    $hex = str_replace('#', '', $hex);
    return [
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2))
    ];
}

// Function to generate preview image
function generatePreviewImage($theme, $config, $width = 600, $height = 400) {
    $image = imagecreatetruecolor($width, $height);
    
    // Convert colors
    $primary = hexToRgb($config['primary']);
    $secondary = hexToRgb($config['secondary']);
    $accent = hexToRgb($config['accent']);
    
    // Create colors
    $primaryColor = imagecolorallocate($image, $primary['r'], $primary['g'], $primary['b']);
    $secondaryColor = imagecolorallocate($image, $secondary['r'], $secondary['g'], $secondary['b']);
    $accentColor = imagecolorallocate($image, $accent['r'], $accent['g'], $accent['b']);
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $gray = imagecolorallocate($image, 128, 128, 128);
    
    // Create gradient background
    for ($i = 0; $i < $height; $i++) {
        $ratio = $i / $height;
        $r = $primary['r'] * (1 - $ratio) + $secondary['r'] * $ratio;
        $g = $primary['g'] * (1 - $ratio) + $secondary['g'] * $ratio;
        $b = $primary['b'] * (1 - $ratio) + $secondary['b'] * $ratio;
        
        $color = imagecolorallocate($image, $r, $g, $b);
        imageline($image, 0, $i, $width, $i, $color);
    }
    
    // Add header bar
    imagefilledrectangle($image, 0, 0, $width, 60, $white);
    
    // Add navigation dots
    for ($i = 0; $i < 4; $i++) {
        imagefilledellipse($image, 50 + ($i * 40), 30, 20, 20, $primaryColor);
    }
    
    // Add product cards simulation
    $cardWidth = 120;
    $cardHeight = 150;
    $padding = 20;
    $startX = 50;
    $startY = 100;
    
    for ($row = 0; $row < 2; $row++) {
        for ($col = 0; $col < 4; $col++) {
            $x = $startX + ($col * ($cardWidth + $padding));
            $y = $startY + ($row * ($cardHeight + $padding));
            
            // Draw card background
            imagefilledrectangle($image, $x, $y, $x + $cardWidth, $y + $cardHeight, $white);
            
            // Draw card image area
            imagefilledrectangle($image, $x + 5, $y + 5, $x + $cardWidth - 5, $y + 80, $accentColor);
            
            // Draw card text lines
            imagefilledrectangle($image, $x + 10, $y + 90, $x + $cardWidth - 10, $y + 95, $gray);
            imagefilledrectangle($image, $x + 10, $y + 100, $x + $cardWidth - 30, $y + 105, $gray);
            
            // Draw price area
            imagefilledrectangle($image, $x + 10, $y + 120, $x + 40, $y + 135, $primaryColor);
        }
    }
    
    // Add footer
    imagefilledrectangle($image, 0, $height - 40, $width, $height, $secondaryColor);
    
    // Add some accent elements
    imagefilledellipse($image, $width - 50, 80, 30, 30, $accentColor);
    imagefilledellipse($image, $width - 100, 120, 20, 20, $accentColor);
    
    // Save the image
    $filename = __DIR__ . '/../public/images/themes/' . $theme . '.jpg';
    imagejpeg($image, $filename, 90);
    imagedestroy($image);
    
    return $filename;
}

// Generate images for all themes
foreach ($themes as $slug => $config) {
    $filename = generatePreviewImage($slug, $config);
    echo "Generated preview image for {$config['name']}: {$filename}\n";
}

echo "All theme preview images generated successfully!\n";
?>
