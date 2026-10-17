<?php
/**
 * API: Projetos ativos
 * GET /api/projetos.php
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . APP_URL);

try {
    $projetos = db_fetch_all(
        'SELECT id, nome_projeto FROM tab_projetos WHERE ativo = 1 ORDER BY nome_projeto',
        []
    );
    echo json_encode($projetos);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar projetos']);
}
