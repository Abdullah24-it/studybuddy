<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Router;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    foreach (['REDIRECT_HTTP_AUTHORIZATION', 'REDIRECT_REDIRECT_HTTP_AUTHORIZATION'] as $key) {
        if (!empty($_SERVER[$key])) {
            $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER[$key];
            break;
        }
    }
    if (!isset($_SERVER['HTTP_AUTHORIZATION']) && function_exists('apache_request_headers')) {
        foreach (apache_request_headers() as $k => $v) {
            if (strtolower($k) === 'authorization') {
                $_SERVER['HTTP_AUTHORIZATION'] = $v;
                break;
            }
        }
    }
}

$router = new Router();
$router->dispatch();