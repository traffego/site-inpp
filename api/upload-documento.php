<?php
/**
 * API: Upload de documento (PDF) para projeto
 * POST /api/upload-documento.php
 * Requer autenticação admin
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
require_once ROOT_PATH . '/src/upload.php';

header('Content-Type: application/json; charset=utf-8');

// Exige autenticação
if (!auth_check()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

// Verificar CSRF
$token_post   = $_POST['csrf_token'] ?? '';
$token_sessao = $_SESSION['csrf_token'] ?? '';
if (empty($token_post) || !hash_equals($token_sessao, $token_post)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Token CSRF inválido']);
    exit;
}

$projeto_id    = (int)($_POST['projeto_id'] ?? 0);
$grupo_id      = (int)($_POST['grupo_id'] ?? 0);
$tipo_id       = (int)($_POST['tipo_id'] ?? 0);
$nome_documento = trim($_POST['nome_documento'] ?? '');

if (!$projeto_id || !$grupo_id || !$tipo_id || empty($nome_documento)) {
    echo json_encode(['success' => false, 'error' => 'Campos obrigatórios não preenchidos']);
    exit;
}

if (empty($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Arquivo não enviado']);
    exit;
}

// Upload do PDF
$resultado = upload_file(
    $_FILES['arquivo'],
    UPLOAD_PATH . '/projetos',
    ['pdf']
);

if (!$resultado['success']) {
    echo json_encode(['success' => false, 'error' => $resultado['error']]);
    exit;
}

// Inserir no banco
try {
    $id = db_insert('tab_projetos_documentos', [
        'id_projeto'         => $projeto_id,
        'id_grupo_documento' => $grupo_id,
        'id_tipo_documento'  => $tipo_id,
        'nome_documento'     => $nome_documento,
        'arquivo'            => $resultado['filename'],
    ]);
    echo json_encode(['success' => true, 'id' => $id, 'arquivo' => $resultado['filename']]);
} catch (Exception $e) {
    // Remover arquivo se falhar inserção
    @unlink(UPLOAD_PATH . '/projetos/' . $resultado['filename']);
    echo json_encode(['success' => false, 'error' => 'Erro ao salvar no banco de dados']);
}
