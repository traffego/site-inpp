<?php
/**
 * install.php — Instalador do sistema IAPS
 *
 * ⚠️  APAGUE ESTE ARQUIVO APÓS A INSTALAÇÃO!
 *
 * Executa em ordem:
 *   1. migrations/001_schema.sql  (criação das tabelas)
 *   2. migrations/002_seed_domain.sql (dados iniciais)
 *   3. migrations/003_import_cities.sql (cidades e estados)
 *   4. migrations/004_seed_projects_and_documents.sql (projetos e editais)
 */

// ─── Segurança: bloquear em produção com token ───────────────────────────────
define('INSTALL_TOKEN', 'iaps2025');   // ← troque se quiser
$token = $_GET['token'] ?? '';
if ($token !== INSTALL_TOKEN) {
    http_response_code(403);
    die('<h2>Acesso negado.</h2><p>Use: <code>install.php?token=' . INSTALL_TOKEN . '</code></p>');
}

// ─── Carregar configuração ────────────────────────────────────────────────────
$env_file = __DIR__ . '/.env';
if (!file_exists($env_file)) {
    die('<h2>❌ Arquivo .env não encontrado!</h2><p>Crie o arquivo .env baseado no .env.example antes de continuar.</p>');
}

// Parser simples de .env
foreach (file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
    if (str_starts_with(trim($linha), '#') || !str_contains($linha, '=')) continue;
    [$chave, $valor] = explode('=', $linha, 2);
    putenv(trim($chave) . '=' . trim($valor));
}

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: '';
$user = getenv('DB_USER') ?: '';
$pass = getenv('DB_PASS') ?: '';

// ─── Migrations a executar (em ordem) ────────────────────────────────────────
$migrations = [
    '001_schema.sql'                       => 'Criação das tabelas',
    '002_seed_domain.sql'                  => 'Dados iniciais (estados, funções, organização, admin)',
    '003_import_cities.sql'                => 'Cidades e estados',
    '004_seed_projects_and_documents.sql'  => 'Importação dos projetos e documentos esportivos',
    '005_seed_curriculos_fornecedores.sql'  => 'Importação de currículos e fornecedores',
    '006_seed_transparencia_docs.sql'       => 'Importação dos documentos de transparência institucionais',
];

// ─── Conectar ao banco ────────────────────────────────────────────────────────
try {
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('<h2>❌ Erro de conexão com o banco</h2><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}

// ─── Função para executar arquivo SQL ────────────────────────────────────────
function executar_sql(PDO $pdo, string $arquivo): array {
    $log     = [];
    $caminho = __DIR__ . '/migrations/' . $arquivo;

    if (!file_exists($caminho)) {
        return [['ok' => false, 'msg' => "Arquivo não encontrado: {$arquivo}"]];
    }

    $sql_raw = file_get_contents($caminho);

    // Remover comentários de linha dupla (--)
    $sql_raw = preg_replace('/^\s*--.*$/m', '', $sql_raw);

    // Dividir por ; (statements individuais)
    $statements = array_filter(
        array_map('trim', explode(';', $sql_raw)),
        fn($s) => !empty($s)
    );

    foreach ($statements as $stmt) {
        try {
            $pdo->exec($stmt);
            $primeira = strtok($stmt, "\n");
            $log[] = ['ok' => true, 'msg' => htmlspecialchars(substr($primeira, 0, 100))];
        } catch (PDOException $e) {
            $codigo = (int)$e->getCode();
            if (in_array($codigo, [1050, 1060, 1061, 1062, 1068])) {
                $primeira = strtok($stmt, "\n");
                $log[] = ['ok' => null, 'msg' => '(já existe) ' . htmlspecialchars(substr($primeira, 0, 80))];
            } else {
                $log[] = ['ok' => false, 'msg' => htmlspecialchars($e->getMessage())];
            }
        }
    }

    return $log;
}

// ─── Executar migrations ──────────────────────────────────────────────────────
$resultados = [];
foreach ($migrations as $arquivo => $descricao) {
    $resultados[$arquivo] = [
        'descricao' => $descricao,
        'log'       => executar_sql($pdo, $arquivo),
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IAPS — Instalador</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0 }
        body { font-family: system-ui, sans-serif; background: #0f172a; color: #e2e8f0; padding: 2rem }
        h1 { color: #10b981; margin-bottom: 2rem; font-size: 1.5rem }
        .migration { background: #1e293b; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid #334155 }
        .migration h2 { font-size: 1rem; color: #94a3b8; margin-bottom: .25rem }
        .migration h3 { font-size: .8rem; color: #64748b; font-weight: normal; margin-bottom: 1rem }
        .log-item { padding: .3rem .6rem; border-radius: 6px; font-size: .8rem; font-family: monospace; margin-bottom: .25rem }
        .ok   { background: #052e16; color: #86efac }
        .skip { background: #1e1b4b; color: #a5b4fc }
        .err  { background: #450a0a; color: #fca5a5 }
    </style>
</head>
<body>
    <h1>IAPS — Instalação do Banco de Dados</h1>
    <?php foreach ($resultados as $arquivo => $info): ?>
    <div class="migration">
        <h2><?= htmlspecialchars($arquivo) ?></h2>
        <h3><?= htmlspecialchars($info['descricao']) ?></h3>
        <?php foreach ($info['log'] as $item): ?>
            <?php
            $classe = $item['ok'] === true ? 'ok' : ($item['ok'] === null ? 'skip' : 'err');
            $icone  = $item['ok'] === true ? '✓' : ($item['ok'] === null ? '➔' : '✗');
            ?>
            <div class="log-item <?= $classe ?>">
                <?= $icone ?> <?= $item['msg'] ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
    <p style="margin-top:2rem;color:#10b981;font-weight:bold;">✅ Migração concluída com sucesso!</p>
</body>
</html>
