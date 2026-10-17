<?php
// admin/logout.php — Encerra a sessão e redireciona ao login
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/auth.php';

auth_logout();
// auth_logout() já chama redirect internamente, mas garantimos aqui
redirect('/admin/index.php');
