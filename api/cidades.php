<?php
/**
 * API: Cidades por UF
 * Proxy com cache (24h) para a API do IBGE
 * GET /api/cidades.php?uf=XX
 *
 * IBGE: https://servicodados.ibge.gov.br/api/v1/localidades/estados/{UF}/municipios
 */
require_once dirname(__DIR__) . '/src/config.php';

header('Content-Type: application/json; charset=utf-8');

$uf = strtoupper(trim($_GET['uf'] ?? ''));

// Validar UF: 2 letras
if (empty($uf) || strlen($uf) !== 2 || !ctype_alpha($uf)) {
    echo json_encode([]);
    exit;
}

// Diretório de cache
$cache_dir  = ROOT_PATH . '/storage/cache/cidades';
$cache_file = $cache_dir . '/' . $uf . '.json';
$cache_ttl  = 86400; // 24 horas

// Criar diretório de cache se não existir
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0755, true);
}

// Retornar cache válido se existir
if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_ttl) {
    readfile($cache_file);
    exit;
}

// Chamar API do IBGE
$url      = "https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$uf}/municipios?orderBy=nome";
$contexto = stream_context_create([
    'http' => [
        'timeout'        => 10,
        'method'         => 'GET',
        'ignore_errors'  => true,
        'header'         => "Accept: application/json\r\nUser-Agent: IAPS-Site/1.0\r\n",
    ],
    'ssl' => [
        'verify_peer'      => true,
        'verify_peer_name' => true,
    ],
]);

$resposta = @file_get_contents($url, false, $contexto);

if ($resposta === false) {
    // IBGE indisponível — tentar cache expirado se existir
    if (file_exists($cache_file)) {
        readfile($cache_file);
    } else {
        http_response_code(503);
        echo json_encode(['error' => 'Serviço de cidades temporariamente indisponível.']);
    }
    exit;
}

// Decodificar e transformar para formato simples [{id, nome}]
$dados = json_decode($resposta, true);

if (!is_array($dados)) {
    http_response_code(502);
    echo json_encode(['error' => 'Resposta inválida da API IBGE.']);
    exit;
}

$cidades = array_map(fn($m) => [
    'id'   => (int)$m['id'],
    'nome' => (string)$m['nome'],
], $dados);

$json = json_encode($cidades, JSON_UNESCAPED_UNICODE);

// Salvar no cache
file_put_contents($cache_file, $json);

echo $json;
