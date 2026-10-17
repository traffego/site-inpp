<?php
// src/config.php

// Função para carregar o .env (sem dependências)
function carregar_env(string $caminho): void {
    if (!file_exists($caminho)) {
        return;
    }
    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        if (str_starts_with(trim($linha), '#')) {
            continue;
        }
        if (str_contains($linha, '=')) {
            [$chave, $valor] = explode('=', $linha, 2);
            $chave = trim($chave);
            $valor = trim($valor);
            // Remove aspas
            $valor = trim($valor, '"\'');
            putenv(sprintf('%s=%s', $chave, $valor));
            $_ENV[$chave] = $valor;
            $_SERVER[$chave] = $valor;
        }
    }
}

// Constantes de caminhos
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'src');
define('PUBLIC_PATH', ROOT_PATH);
define('UPLOAD_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads');

// Carrega variáveis do ambiente, preferencialmente do .env
$env_file = ROOT_PATH . DIRECTORY_SEPARATOR . '.env';
if (!file_exists($env_file)) {
    $env_file = ROOT_PATH . DIRECTORY_SEPARATOR . '.env.example';
}
carregar_env($env_file);

// Constantes do Banco de Dados
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'INPP_db');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_PORT', $_ENV['DB_PORT'] ?? '3306');

// Constantes SMTP
define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com');
define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? '587');
define('SMTP_USER', $_ENV['SMTP_USER'] ?? '');
define('SMTP_PASS', $_ENV['SMTP_PASS'] ?? '');
define('SMTP_FROM', $_ENV['SMTP_FROM'] ?? 'contato@inpp.org');
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? 'Instituto Nacional de Políticas Públicas');

// Outros
define('TINYMCE_API_KEY', $_ENV['TINYMCE_API_KEY'] ?? '');
define('CONTACT_EMAIL', $_ENV['CONTACT_EMAIL'] ?? 'contato@inpp.org');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost:8080');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));

// Configurações do PHP
date_default_timezone_set('America/Sao_Paulo');

if (APP_ENV === 'development' || APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Configurações de Sessão Seguras
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_samesite', 'Lax');
if (str_starts_with(APP_URL, 'https')) {
    ini_set('session.cookie_secure', '1');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
