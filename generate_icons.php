<?php

function generateIcon($size, $filename, $isMaskable = false) {
    $img = imagecreatetruecolor($size, $size);

    // Gradient emulation (navy/blue) - linear top to bottom
    // We'll draw filled rectangles for the gradient
    $colorTop = [79, 70, 229]; // indigo-600
    $colorBottom = [236, 72, 153]; // pink-500
    
    // Instead of complex gradient, let's just make it a solid modern blue: indigo-600 #4f46e5
    // Wait, let's use a nice gradient since it's easy in PHP GD by drawing lines
    for ($y = 0; $y < $size; $y++) {
        $r = $colorTop[0] + (($colorBottom[0] - $colorTop[0]) * ($y / $size));
        $g = $colorTop[1] + (($colorBottom[1] - $colorTop[1]) * ($y / $size));
        $b = $colorTop[2] + (($colorBottom[2] - $colorTop[2]) * ($y / $size));
        $col = imagecolorallocate($img, (int)$r, (int)$g, (int)$b);
        imageline($img, 0, $y, $size, $y, $col);
    }

    // Determine content area size
    $safeArea = $isMaskable ? $size * 0.7 : $size * 0.9;
    
    // Draw "K"
    $textColor = imagecolorallocate($img, 255, 255, 255);
    // Since we can't guarantee font, we'll draw simple geometry for the 'K'
    // or just use built-in font if large enough. Built-in is too small.
    // Let's draw thick lines for 'K'
    imagesetthickness($img, max(1, $size / 15));
    
    $centerX = $size / 2;
    $centerY = $size / 2;
    $kHeight = $safeArea * 0.6;
    $kWidth = $safeArea * 0.5;
    
    $startX = $centerX - ($kWidth / 2);
    $startY = $centerY - ($kHeight / 2);
    
    // Vertical line
    imageline($img, $startX, $startY, $startX, $startY + $kHeight, $textColor);
    
    // Top diagonal
    imageline($img, $startX, $centerY, $startX + $kWidth, $startY, $textColor);
    
    // Bottom diagonal
    imageline($img, $startX, $centerY, $startX + $kWidth, $startY + $kHeight, $textColor);
    
    // If NOT maskable, we can round the corners slightly or just keep square
    // For apple-touch-icon and standard icons, square is fine, maskable needs to be full bleed
    
    imagepng($img, __DIR__ . '/public/' . $filename);
    imagedestroy($img);
    echo "Generated $filename\n";
}

@mkdir(__DIR__ . '/public/images');
@mkdir(__DIR__ . '/public/images/icons');

generateIcon(192, 'images/icons/icon-192.png');
generateIcon(512, 'images/icons/icon-512.png');
generateIcon(512, 'images/icons/icon-maskable-512.png', true);
generateIcon(180, 'apple-touch-icon.png');
generateIcon(64, 'favicon.png');
