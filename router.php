<?php
// router.php - Roteador para servidor embutido do PHP
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = __DIR__ . $uri;

if ($uri !== '/' && file_exists($path)) {
    if (is_dir($path)) {
        if (file_exists($path . '/index.php')) {
            require $path . '/index.php';
            return true;
        }
    } else {
        return false; // Arquivo estático (CSS, JS, imagem, PDF, etc.)
    }
}

// Encaminha para o Front Controller principal
require_once __DIR__ . '/index.php';
