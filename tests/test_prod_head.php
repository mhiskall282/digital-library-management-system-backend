<?php

putenv("APP_ENV=production");
putenv("APP_DEBUG=false");
putenv("APP_URL=https://uew-digital-library.onrender.com");

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$server = [
    'HTTP_HOST' => 'uew-digital-library.onrender.com',
    'HTTP_X_FORWARDED_PROTO' => 'https',
    'HTTPS' => 'on',
    'SERVER_PORT' => 443,
];

$req = Illuminate\Http\Request::create('https://uew-digital-library.onrender.com/', 'GET', [], [], [], $server);
$res = $kernel->handle($req);
$content = $res->getContent();

echo "Status: " . $res->getStatusCode() . "\n";

// Check for http:// links
preg_match_all('/(href|src)="([^"]+)"/', $content, $matches);
$httpLinks = array_filter($matches[2], fn($url) => str_starts_with($url, 'http://uew-digital-library'));

if (count($httpLinks) === 0) {
    echo "SUCCESS: Zero insecure http:// links found! All assets & links are HTTPS.\n";
} else {
    echo "WARNING: Found insecure http:// links:\n";
    foreach ($httpLinks as $link) echo "  - $link\n";
}

// Print asset tags
preg_match_all('/<(link|script)[^>]+build\/assets[^>]+>/', $content, $assetMatches);
echo "\nRendered Asset Tags:\n";
foreach ($assetMatches[0] as $tag) {
    echo "  " . $tag . "\n";
}
