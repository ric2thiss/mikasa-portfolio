<?php
/**
 * Dynamic Image Thumbnail Generator
 * Usage: thumb.php?src=assets/images/portfolio/photo.jpg&w=400&q=70
 * 
 * Generates and caches resized thumbnails on the fly.
 * This dramatically reduces bandwidth for gallery pages.
 */

// Validate inputs
$src = $_GET['src'] ?? '';
$width = min((int)($_GET['w'] ?? 800), 1920);  // max 1920px
$quality = min((int)($_GET['q'] ?? 75), 95);     // max quality 95

if (!$src || $width < 10) {
    http_response_code(400);
    exit('Invalid parameters');
}

// Security: only allow images from assets directory
$src = ltrim($src, '/');
if (strpos($src, '..') !== false || !preg_match('/^assets\//', $src)) {
    http_response_code(403);
    exit('Forbidden');
}

$sourcePath = __DIR__ . '/' . $src;
if (!file_exists($sourcePath)) {
    http_response_code(404);
    exit('Not found');
}

// Create cache directory
$cacheDir = __DIR__ . '/assets/cache/thumbs/';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// Generate cache filename based on source + dimensions + quality
$cacheKey = md5($src . $width . $quality . filemtime($sourcePath));
$ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));

// Determine output format — prefer WebP for smaller file sizes
$supportsWebp = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false;
$outputExt = $supportsWebp ? 'webp' : $ext;
$cachePath = $cacheDir . $cacheKey . '.' . $outputExt;

// Serve from cache if available
if (file_exists($cachePath)) {
    $mime = $outputExt === 'webp' ? 'image/webp' : ($ext === 'png' ? 'image/png' : 'image/jpeg');
    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=31536000, immutable');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
    header('Content-Length: ' . filesize($cachePath));
    readfile($cachePath);
    exit;
}

// Generate thumbnail
$info = @getimagesize($sourcePath);
if (!$info) {
    // Not an image — just serve original
    header('Location: ' . $src);
    exit;
}

$origWidth = $info[0];
$origHeight = $info[1];
$mime = $info['mime'];

// If original is already smaller than requested width, serve original
if ($origWidth <= $width) {
    header('Location: ' . $src);
    exit;
}

// Calculate proportional height
$ratio = $origHeight / $origWidth;
$newWidth = $width;
$newHeight = (int)round($width * $ratio);

// Create source image
switch ($mime) {
    case 'image/jpeg':
        $sourceImg = @imagecreatefromjpeg($sourcePath);
        break;
    case 'image/png':
        $sourceImg = @imagecreatefrompng($sourcePath);
        break;
    case 'image/webp':
        $sourceImg = @imagecreatefromwebp($sourcePath);
        break;
    case 'image/gif':
        $sourceImg = @imagecreatefromgif($sourcePath);
        break;
    default:
        header('Location: ' . $src);
        exit;
}

if (!$sourceImg) {
    header('Location: ' . $src);
    exit;
}

// Create resized image
$thumb = imagecreatetruecolor($newWidth, $newHeight);

// Preserve transparency for PNG
if ($mime === 'image/png') {
    imagealphablending($thumb, false);
    imagesavealpha($thumb, true);
}

imagecopyresampled($thumb, $sourceImg, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

// Save to cache
if ($supportsWebp && function_exists('imagewebp')) {
    imagewebp($thumb, $cachePath, $quality);
    header('Content-Type: image/webp');
} elseif ($ext === 'png') {
    $pngQuality = (int)round((100 - $quality) / 11.1); // Convert to 0-9 scale
    imagepng($thumb, $cachePath, $pngQuality);
    header('Content-Type: image/png');
} else {
    imagejpeg($thumb, $cachePath, $quality);
    header('Content-Type: image/jpeg');
}

imagedestroy($sourceImg);
imagedestroy($thumb);

// Serve the cached file
header('Cache-Control: public, max-age=31536000, immutable');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
header('Content-Length: ' . filesize($cachePath));
readfile($cachePath);
