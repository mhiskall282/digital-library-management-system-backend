<?php

putenv("APP_ENV=production");
putenv("APP_DEBUG=false");

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "1. Testing HEAD /health ...\n";
$req = Illuminate\Http\Request::create('/health', 'HEAD');
$res = $kernel->handle($req);
echo "   Status: " . $res->getStatusCode() . "\n";

echo "2. Testing HEAD / ...\n";
$req = Illuminate\Http\Request::create('/', 'HEAD');
$res = $kernel->handle($req);
echo "   Status: " . $res->getStatusCode() . "\n";

echo "3. Testing GET /health ...\n";
$req = Illuminate\Http\Request::create('/health', 'GET');
$res = $kernel->handle($req);
echo "   Status: " . $res->getStatusCode() . "\n";
echo "   Body: " . $res->getContent() . "\n";

echo "All passed without exception.\n";
