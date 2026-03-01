<?php
/**
 * Generate PWA Icons
 * 
 * Uso: php generate-icons.php
 * Gera icon-192x192.png, icon-512x512.png, maskable-icon-192x192.png
 */

$sizes = [192, 512];
$colors = [
  'primary' => [59, 130, 246],    // blue-500
  'accent' => [236, 72, 153]      // pink-500
];

function createIcon($size, $filename, $colors) {
    // Create image with transparent background
    $img = imagecreatetruecolor($size, $size);
    
    // Enable transparency
    imagecolortransparent($img, imagecolorallocate($img, 0, 0, 0));
    imagealphablending($img, false);
    imagesavealpha($img, true);
    
    // Fill with primary color
    $primaryColor = imagecolorallocatealpha(
        $img, 
        $colors['primary'][0], 
        $colors['primary'][1], 
        $colors['primary'][2], 
        0
    );
    
    // Fill background
    imagefill($img, 0, 0, $primaryColor);
    
    // Create accent circle
    $accentColor = imagecolorallocatealpha(
        $img,
        $colors['accent'][0],
        $colors['accent'][1],
        $colors['accent'][2],
        0
    );
    
    // Draw outer circle (accent)
    imagefilledellipse($img, $size/2, $size/2, $size * 0.8, $size * 0.8, $accentColor);
    
    // Draw inner circle (primary - for contrast)
    imagefilledellipse($img, $size/2, $size/2, $size * 0.6, $size * 0.6, $primaryColor);
    
    // Add white letter "T" (TeamCore)
    $whiteColor = imagecolorallocate($img, 255, 255, 255);
    
    // Simple T shape
    $padding = $size * 0.15;
    $lineWidth = max(1, $size / 40);
    
    // Horizontal line (top of T)
    imagefilledrectangle(
        $img,
        $size/2 - $size*0.2,
        $size/2 - $size*0.25,
        $size/2 + $size*0.2,
        $size/2 - $size*0.2,
        $whiteColor
    );
    
    // Vertical line (stem of T)
    imagefilledrectangle(
        $img,
        $size/2 - $size*0.05,
        $size/2 - $size*0.2,
        $size/2 + $size*0.05,
        $size/2 + $size*0.25,
        $whiteColor
    );
    
    // Save PNG
    imagepng($img, $filename, 9);
    /** @phpstan-ignore-next-line imagedestroy is safe for cleanup */
    imagedestroy($img);
    
    echo "✓ Generated: {$filename}\n";
}

try {
    // Create 192x192
    createIcon(192, __DIR__ . '/icon-192x192.png', $colors);
    
    // Create 512x512
    createIcon(512, __DIR__ . '/icon-512x512.png', $colors);
    
    // Create maskable 192x192 (same as regular but with padding for iOS)
    createIcon(192, __DIR__ . '/maskable-icon-192x192.png', $colors);
    
    echo "\n✅ All PWA icons generated successfully!\n";
    echo "   • icon-192x192.png\n";
    echo "   • icon-512x512.png\n";
    echo "   • maskable-icon-192x192.png\n";
    
} catch (Exception $e) {
    echo "❌ Error generating icons: " . $e->getMessage() . "\n";
    exit(1);
}

