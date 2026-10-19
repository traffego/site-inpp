<?php
// index.php - Front Controller Principal

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/database.php';
require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/csrf.php';

// Analisar URI
$request_uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Remover base_dir se site estiver em subpasta
$base_dir = parse_url(APP_URL, PHP_URL_PATH);
if ($base_dir && $base_dir !== '/' && str_starts_with($request_uri, $base_dir)) {
    $request_uri = substr($request_uri, strlen($base_dir));
}

if ($request_uri === '' || $request_uri === null) {
    $request_uri = '/';
}

// Mapa de rotas → arquivo de página
$routes = [
    '/'                                  => 'pages/home.php',
    '/home2'                             => 'pages/home2.php',
    '/projetos'                          => 'pages/projetos.php',
    '/quem-somos'                        => 'pages/quem-somos.php',
    '/contato'                           => 'pages/contato.php',
    '/noticias'                          => 'pages/noticias.php',
    '/editais'                           => 'pages/editais.php',
    '/trabalhe-conosco'                  => 'pages/trabalhe-conosco.php',
    '/fornecedores'                      => 'pages/fornecedores.php',
    '/transparencia/declaracao'          => 'pages/transparencia/declaracao.php',
    '/transparencia/dirigentes'          => 'pages/transparencia/dirigentes.php',
    '/transparencia/estatuto'            => 'pages/transparencia/estatuto.php',
    '/transparencia/financeiro'          => 'pages/transparencia/financeiro.php',
    '/transparencia/regulamento'         => 'pages/transparencia/regulamento.php',
    '/transparencia/termos'              => 'pages/transparencia/termos.php',
    '/transparencia/painel-transferencias' => 'pages/transparencia/painel-transferencias.php',
    '/transparencia/painel'              => 'pages/transparencia/painel-transferencias.php',
];

$file_to_include = null;

// Rota exata
if (isset($routes[$request_uri])) {
    $file_to_include = $routes[$request_uri];
}

// Rotas dinâmicas
if (!$file_to_include) {
    if (preg_match('#^/noticia/(\d+)$#', $request_uri, $m)) {
        $_GET['id']      = (int)$m[1];
        $file_to_include = 'pages/noticia-detalhe.php';
    } elseif (preg_match('#^/projetos/(\d+)$#', $request_uri, $m)) {
        $_GET['projeto'] = (int)$m[1];
        $file_to_include = 'pages/editais.php';
    }
}

// Executar página ou 404
if ($file_to_include) {
    if (str_starts_with($request_uri, '/transparencia/')) {
        $transp_slug = explode('/', substr($request_uri, strlen('/transparencia/')))[0];
        if (!is_transparencia_habilitada($transp_slug)) {
            $file_to_include = null;
        }
    } elseif ($request_uri === '/editais') {
        if (!is_transparencia_habilitada('editais')) {
            $file_to_include = null;
        }
    } elseif ($request_uri === '/projetos' || str_starts_with($request_uri, '/projetos/')) {
        if (!is_transparencia_habilitada('projetos')) {
            $file_to_include = null;
        }
    } elseif ($request_uri === '/noticias' || str_starts_with($request_uri, '/noticia/')) {
        if (!is_transparencia_habilitada('noticias')) {
            $file_to_include = null;
        }
    }
}

if ($file_to_include && file_exists(__DIR__ . '/' . $file_to_include)) {
    require __DIR__ . '/' . $file_to_include;
} else {

    http_response_code(404);
    $page_title       = 'Página não encontrada';
    $page_description = 'A URL solicitada não existe.';
    ob_start();
    echo '<section class="section"><div class="container" style="text-align:center;padding:6rem 1rem">';
    echo '<h1 style="font-size:5rem;color:var(--color-primary)">404</h1>';
    echo '<p style="font-size:1.25rem;margin-bottom:2rem">Página não encontrada.</p>';
    echo '<a href="/" class="btn btn-primary">Voltar ao início</a>';
    echo '</div></section>';
    $content = ob_get_clean();
    require __DIR__ . '/templates/layout.php';
}
