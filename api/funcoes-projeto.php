<?php
/**
 * API: Funções vinculadas a um projeto
 * GET /api/funcoes-projeto.php?projeto_id=X
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';

header('Content-Type: application/json; charset=utf-8');

$projeto_id = (int)($_GET['projeto_id'] ?? 0);

try {
    $funcoes = [];

    if ($projeto_id > 0) {
        // 1. Buscar funções únicas vinculadas ao projeto (DISTINCT / GROUP BY para evitar repetições por vagas)
        $funcoes = db_fetch_all(
            'SELECT DISTINCT f.id, f.funcao
             FROM tab_curriculos_funcao f
             INNER JOIN tab_projetos_funcao pf ON f.id = pf.id_funcao
             WHERE pf.id_projeto = ? AND f.ativo = 1
             GROUP BY f.id, f.funcao
             ORDER BY f.funcao',
            [$projeto_id]
        );
    }

    // 2. Se o projeto não tiver mapeamento específico ou se nenhum projeto for informado,
    // retornar todas as funções ativas únicas como fallback
    if (empty($funcoes)) {
        $funcoes = db_fetch_all(
            'SELECT DISTINCT id, funcao FROM tab_curriculos_funcao WHERE ativo = 1 GROUP BY id, funcao ORDER BY funcao'
        );
    }

    // 3. Garantir unicidade estrita por nome da função em PHP (eliminar duplicatas de nomes repetidos no legado)
    $funcoes_unicas = [];
    $nomes_vistos = [];

    foreach ($funcoes as $f) {
        $nome_normalizado = mb_strtolower(trim($f['funcao']));
        if (!in_array($nome_normalizado, $nomes_vistos)) {
            $nomes_vistos[] = $nome_normalizado;
            $funcoes_unicas[] = [
                'id'     => (int)$f['id'],
                'funcao' => trim($f['funcao'])
            ];
        }
    }

    echo json_encode($funcoes_unicas);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar funções']);
}
