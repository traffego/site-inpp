<?php
// src/helpers.php

function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never {
    header(sprintf('Location: %s', $url));
    exit;
}

function flash(string $key, ?string $message = null): ?string {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    
    return null;
}

function old(string $key, mixed $default = ''): mixed {
    $old = $_SESSION['old_input'][$key] ?? $default;
    unset($_SESSION['old_input'][$key]); // Apaga ao usar
    return $old;
}

function save_old_input(): void {
    $_SESSION['old_input'] = $_POST;
}

function is_current_page(string $page): bool {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($page === '/') {
        return $uri === '/';
    }
    return str_starts_with($uri, $page);
}

function format_date(string $date, string $format = 'd/m/Y'): string {
    $timestamp = strtotime($date);
    return $timestamp ? date($format, $timestamp) : '';
}

function format_money(float $value): string {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function truncate(string $string, int $length = 100): string {
    if (mb_strlen($string) <= $length) {
        return $string;
    }
    return mb_substr($string, 0, $length) . '...';
}

function slug(string $string): string {
    // Substitui caracteres especiais
    $map = [
        '/[áàâãä]/u' => 'a', '/[ÁÀÂÃÄ]/u' => 'A',
        '/[éèêë]/u' => 'e', '/[ÉÈÊË]/u' => 'E',
        '/[íìîï]/u' => 'i', '/[ÍÌÎÏ]/u' => 'I',
        '/[óòôõö]/u' => 'o', '/[ÓÒÔÕÖ]/u' => 'O',
        '/[úùûü]/u' => 'u', '/[ÚÙÛÜ]/u' => 'U',
        '/ç/' => 'c', '/Ç/' => 'C', '/ñ/' => 'n', '/Ñ/' => 'N'
    ];
    $string = preg_replace(array_keys($map), array_values($map), $string);
    $string = strtolower(trim($string));
    // Troca tudo que não for letra e número por traço
    $string = preg_replace('/[^a-z0-9-]+/', '-', $string);
    // Remove traços duplos
    return preg_replace('/-+/', '-', $string);
}

function paginate(int $total, int $per_page = 20): array {
    $current_page = max(1, (int)($_GET['pagina'] ?? 1));
    $total_pages = max(1, ceil($total / $per_page));
    
    // Garante que a página atual não ultrapasse o total
    if ($current_page > $total_pages) {
        $current_page = (int) $total_pages;
    }
    
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'current_page' => $current_page,
        'total_pages' => (int) $total_pages,
        'total' => $total,
        'per_page' => $per_page,
        'offset' => $offset
    ];
}

function pagination_html(array $pagination, string $base_url): string {
    if ($pagination['total_pages'] <= 1) return '';
    
    $html = '<ul class="pagination">';
    
    // Processa a URL base para preservar query strings existentes
    $parsed = parse_url($base_url);
    $path = $parsed['path'] ?? '/';
    $query = [];
    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $query);
    }
    
    $build_url = function($page) use ($path, $query) {
        $query['pagina'] = $page;
        return $path . '?' . http_build_query($query);
    };
    
    if ($pagination['current_page'] > 1) {
        $html .= sprintf('<li><a href="%s">&laquo; Anterior</a></li>', e($build_url($pagination['current_page'] - 1)));
    }
    
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        $active = $i === $pagination['current_page'] ? 'class="active"' : '';
        $html .= sprintf('<li %s><a href="%s">%d</a></li>', $active, e($build_url($i)), $i);
    }
    
    if ($pagination['current_page'] < $pagination['total_pages']) {
        $html .= sprintf('<li><a href="%s">Próxima &raquo;</a></li>', e($build_url($pagination['current_page'] + 1)));
    }
    
    $html .= '</ul>';
    
    return $html;
}

function asset(string $path): string {
    $full_path = defined('ROOT_PATH') ? ROOT_PATH . '/' . ltrim($path, '/') : __DIR__ . '/../' . ltrim($path, '/');
    $v = file_exists($full_path) ? filemtime($full_path) : time();
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/') . '?v=' . $v;
}

function base_url(string $path = ''): string {
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/') . '?v=' . time();
}

function upload_path(string $subdir = ''): string {
    $path = UPLOAD_PATH;
    if ($subdir !== '') {
        $path .= DIRECTORY_SEPARATOR . trim($subdir, '/\\');
    }
    return $path;
}

function is_ajax(): bool {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function json_response(mixed $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Verifica se um módulo ou página de transparência, projetos, editais ou notícias possui conteúdo ativo
 */
function is_transparencia_habilitada(string $slug): bool {
    try {
        switch ($slug) {
            case 'declaracao':
            case 'transparencia':
                // A seção de transparência e a declaração só existem se houver ao menos um módulo de arquivo ativo
                return (
                    is_transparencia_habilitada('dirigentes') ||
                    is_transparencia_habilitada('estatuto') ||
                    is_transparencia_habilitada('regulamento') ||
                    is_transparencia_habilitada('financeiro') ||
                    is_transparencia_habilitada('termos') ||
                    is_transparencia_habilitada('painel')
                );
            case 'dirigentes':
                $arq_hab = db_count("SELECT COUNT(*) FROM tab_transparencia_arquivos WHERE tipo = 'dirigentes' AND habilitado = 1");
                $dir_hab = db_count("SELECT COUNT(*) FROM tab_dirigente WHERE cod_org = 10001");
                return ($arq_hab > 0 || $dir_hab > 0);
            case 'estatuto':
                return db_count("SELECT COUNT(*) FROM tab_transparencia_arquivos WHERE tipo = 'estatuto' AND habilitado = 1") > 0;
            case 'regulamento':
                return db_count("SELECT COUNT(*) FROM tab_transparencia_arquivos WHERE tipo = 'regulamento' AND habilitado = 1") > 0;
            case 'financeiro':
                return db_count("SELECT COUNT(*) FROM tab_documentos_financeiro WHERE habilitado = 1") > 0;
            case 'termos':
                return db_count("SELECT COUNT(*) FROM tab_documentos_termo_colaboracao WHERE habilitado = 1") > 0;
            case 'painel':
            case 'painel-transferencias':
                return db_count("SELECT COUNT(*) FROM tab_transparencia_arquivos WHERE tipo = 'painel' AND habilitado = 1") > 0;
            case 'editais':
                return db_count("SELECT COUNT(*) FROM tab_projetos_documentos WHERE habilitado = 1") > 0;
            case 'projetos':
                return db_count(
                    "SELECT COUNT(*) FROM tab_projetos p 
                     WHERE p.ativo = 1 
                       AND p.id IN (SELECT DISTINCT id_projeto FROM tab_projetos_documentos WHERE habilitado = 1)"
                ) > 0;
            case 'noticias':
                return db_count("SELECT COUNT(*) FROM tab_noticias") > 0;
            default:
                return true;
        }
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Retorna os itens ativos para a sub-navegação da transparência
 */
function get_transparencia_subnav_items(string $active_slug = ''): array {
    $all = [
        'declaracao'  => ['label' => 'Declaração', 'url' => '/transparencia/declaracao'],
        'dirigentes'  => ['label' => 'Dirigentes', 'url' => '/transparencia/dirigentes'],
        'estatuto'    => ['label' => 'Estatuto', 'url' => '/transparencia/estatuto'],
        'financeiro'  => ['label' => 'Financeiro', 'url' => '/transparencia/financeiro'],
        'regulamento' => ['label' => 'Regulamento', 'url' => '/transparencia/regulamento'],
        'termos'      => ['label' => 'Termos', 'url' => '/transparencia/termos'],
        'painel'      => ['label' => 'Painel de Transferências', 'url' => '/transparencia/painel'],
    ];

    $items = [];
    foreach ($all as $slug => $data) {
        if (is_transparencia_habilitada($slug)) {
            $items[] = [
                'slug'   => $slug,
                'label'  => $data['label'],
                'url'    => $data['url'],
                'active' => ($slug === $active_slug),
            ];
        }
    }
    return $items;
}

