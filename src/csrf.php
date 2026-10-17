<?php
// src/csrf.php

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
        }
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    $token = csrf_token();
    return sprintf('<input type="hidden" name="csrf_token" value="%s">', e($token));
}

function csrf_verify(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $post_token = $_POST['csrf_token'] ?? '';
        $session_token = $_SESSION['csrf_token'] ?? '';
        
        if (empty($post_token) || !hash_equals($session_token, $post_token)) {
            http_response_code(403);
            die('Token CSRF inválido. Ação não autorizada.');
        }
    }
}
