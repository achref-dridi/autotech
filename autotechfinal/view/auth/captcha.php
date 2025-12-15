<?php
session_start();

// Generate a random 5-character code (excluding I, O, 1, l to avoid confusion)
$code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
$_SESSION['captcha_code'] = $code;

// Create image
header('Content-type: image/png');
$image = imagecreatetruecolor(150, 50);

// Define colors
$bgColor = imagecolorallocate($image, 240, 240, 240);
$textColor = imagecolorallocate($image, 0, 0, 0);

// Fill background
imagefilledrectangle($image, 0, 0, 150, 50, $bgColor);

// Add text without TrueType font (using imagestring instead)
$fontHeight = 5;
$fontWidth = 8;

// Center the text
$textX = (150 - (strlen($code) * $fontWidth)) / 2;
$textY = (50 - (8 * 2)) / 2;

imagestring($image, $fontHeight, $textX, $textY, $code, $textColor);

// Add noise lines for security
for ($i = 0; $i < 5; $i++) {
    $noiseColor = imagecolorallocate($image, rand(150, 200), rand(150, 200), rand(150, 200));
    imageline($image, rand(0, 150), rand(0, 50), rand(0, 150), rand(0, 50), $noiseColor);
}

// Output and clean up
imagepng($image);
imagedestroy($image);
?>
