<?php
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = ltrim($path, '/');
$staticDirs = ['css', 'js', 'Imagenes', 'uploads'];
foreach ($staticDirs as $dir) {
    if (strpos($path, $dir) === 0) {
        $file = __DIR__ . '/../' . $path;
        if (file_exists($file)) {
            $mime = mime_content_type($file);
            header("Content-Type: $mime");
            readfile($file);
            exit;
        }
    }
}
$phpFile = __DIR__ . '/../' . ($path ?: 'index.php');
if (!file_exists($phpFile)) {
    $phpFile = __DIR__ . '/../index.php';
}
require $phpFile;
