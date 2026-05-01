<?php
// == Make sure we're in the right directory ==
chdir(__DIR__ . '/../');

// == Bootstrap Composer ==
require_once __DIR__ . '/../vendor/autoload.php';

// == Bootstrap Laravel (boot app, but not run it yet) ==
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// == Capture HTTP request from Vercel's serverless env ==
$request = Illuminate\Http\Request::capture();

// == Let Laravel handle the request ==
$response = $kernel->handle($request);

// == Send response back to Vercel ==
$response->send();

$kernel->terminate($request, $response);
