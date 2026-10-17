<?php
// admin/dashboard.php — Painel principal com estatísticas e distribuição de dados otimizada (Tabler UI)
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';

auth_require();

$page_title = 'Admin — Dashboard';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '']
];

// ── Estatísticas Gerais ────────────────────────────────────────────────────────
$total_projetos     = 0;
$total_curriculos   = 0;
$total_fornecedores = 0;
$total_noticias     = 0;

try { $total_projetos     = db_count('SELECT COUNT(*) FROM tab_projetos WHERE ativo = 1'); } catch (Throwable $e) {}
try { $total_curriculos   = db_count('SELECT COUNT(*) FROM tab_curriculos'); } catch (Throwable $e) {}
try { $total_fornecedores = db_count('SELECT COUNT(*) FROM tab_fornecedores'); } catch (Throwable $e) {}
try { $total_noticias     = db_count('SELECT COUNT(*) FROM tab_noticias'); } catch (Throwable $e) {}

// ── Estatísticas de Transparência ─────────────────────────────────────────────
$total_transp_arquivos = 0;
$total_transp_fin      = 0;
$total_transp_termos   = 0;
$total_transp_ativos   = 0;

try { $total_transp_arquivos = db_count('SELECT COUNT(*) FROM tab_transparencia_arquivos'); } catch (Throwable $e) {}
try { $total_transp_fin      = db_count('SELECT COUNT(*) FROM tab_documentos_financeiro'); } catch (Throwable $e) {}
try { $total_transp_termos   = db_count('SELECT COUNT(*) FROM tab_documentos_termo_colaboracao'); } catch (Throwable $e) {}

try {
    $ativos_arq = db_count('SELECT COUNT(*) FROM tab_transparencia_arquivos WHERE habilitado = 1');
    $ativos_fin = db_count('SELECT COUNT(*) FROM tab_documentos_financeiro WHERE habilitado = 1');
    $ativos_ter = db_count('SELECT COUNT(*) FROM tab_documentos_termo_colaboracao WHERE habilitado = 1');
    $total_transp_ativos = $ativos_arq + $ativos_fin + $ativos_ter;
} catch (Throwable $e) {}

$total_docs_geral = $total_transp_arquivos + $total_transp_fin + $total_transp_termos;

// ── Últimos 8 currículos ──────────────────────────────────────────────────────
$ultimos_curriculos = [];
try {
    $ultimos_curriculos = db_fetch_all(
        'SELECT c.id, c.nome, COALESCE(c.telefone_1, "") AS telefone, f.funcao, p.nome_projeto, c.created_at AS data_cadastro
         FROM tab_curriculos c
         LEFT JOIN tab_curriculos_funcao f ON c.id_funcao = f.id
         LEFT JOIN tab_projetos p ON c.id_projeto = p.id
         ORDER BY c.id DESC
         LIMIT 8'
    );
} catch (Throwable $e) {
    try {
        $ultimos_curriculos = db_fetch_all('SELECT id, nome, created_at AS data_cadastro FROM tab_curriculos ORDER BY id DESC LIMIT 8');
    } catch (Throwable $e2) {
        $ultimos_curriculos = [];
    }
}

// ── Últimos 6 acessos ─────────────────────────────────────────────────────────
$ultimos_acessos = [];
try {
    $ultimos_acessos = db_fetch_all(
        'SELECT r.created_at AS data_hora, r.ip, COALESCE(l.nome_usuario, l.usuario, "Administrador") AS usuario
         FROM tab_login_registro r
         LEFT JOIN tab_login l ON r.usuario = l.id
         ORDER BY r.id DESC
         LIMIT 6'
    );
} catch (Throwable $e) {
    $ultimos_acessos = [];
}

// ── Usuário logado ────────────────────────────────────────────────────────────
$usuario_logado = auth_user();
$primeiro_nome  = explode(' ', trim($_SESSION['admin_nome'] ?? 'Gestor'))[0];

ob_start();
?>
<!-- Faixa de Boas-Vindas & Status Operacional -->
<div class="row align-items-center mb-4">
    <div class="col-12 col-md-8">
        <div class="page-pretitle text-secondary">Painel de Controle do Sistema</div>
        <h2 class="page-title text-truncate">
            Olá, <?= htmlspecialchars($primeiro_nome) ?> &bull; Instituto Nacional de Políticas Públicas
        </h2>
        <p class="text-secondary small mb-0 mt-1">
            Visão consolidada de atividades, repositório de transparência e banco de talentos.
        </p>
    </div>
    <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
        <div class="btn-list justify-content-md-end">
            <a href="/admin/transparencia/painel.php" class="btn btn-outline-primary btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                Publicar Documento
            </a>
            <a href="/admin/projetos/criar.php" class="btn btn-primary btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Novo Projeto
            </a>
        </div>
    </div>
</div>

<!-- Grid de Indicadores Principais (Stat Cards) -->
<div class="row row-deck row-cards mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-primary text-white avatar">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">
                            <a href="/admin/projetos/listar.php" class="text-reset text-decoration-none">Projetos Ativos</a>
                        </div>
                        <div class="text-secondary small"><?= $total_projetos ?> cadastrado(s)</div>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/projetos/listar.php" class="text-secondary" title="Ver Projetos">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-green text-white avatar">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">
                            <a href="/admin/curriculos/listar.php" class="text-reset text-decoration-none">Banco de Talentos</a>
                        </div>
                        <div class="text-secondary small"><?= $total_curriculos ?> currículo(s)</div>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/curriculos/listar.php" class="text-secondary" title="Ver Currículos">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-yellow text-white avatar">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">
                            <a href="/admin/fornecedores/listar.php" class="text-reset text-decoration-none">Fornecedores</a>
                        </div>
                        <div class="text-secondary small"><?= $total_fornecedores ?> empresa(s)</div>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/fornecedores/listar.php" class="text-secondary" title="Ver Fornecedores">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-purple text-white avatar">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">
                            <a href="/admin/transparencia/painel.php" class="text-reset text-decoration-none">Transparência</a>
                        </div>
                        <div class="text-secondary small"><?= $total_docs_geral ?> arquivo(s)</div>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/transparencia/painel.php" class="text-secondary" title="Ver Transparência">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Layout Principal de Distribuição 8x4 -->
<div class="row row-cards mb-4">
    <!-- COLUNA PRINCIPAL (8 Colunas) -->
    <div class="col-12 col-xl-8">
        <!-- Tabela de Últimos Currículos Cadastrados -->
        <div class="card mb-4">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Candidatos Recentes</h3>
                    <p class="card-subtitle mb-0">Últimas inscrições recebidas pelo banco de talentos</p>
                </div>
                <div class="card-actions">
                    <a href="/admin/curriculos/listar.php" class="btn btn-outline-secondary btn-sm">
                        Ver Todos (<?= $total_curriculos ?>)
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ms-1"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
            </div>

            <?php if (empty($ultimos_curriculos)): ?>
                <div class="card-body text-center text-secondary py-5">
                    Nenhum currículo cadastrado até o momento.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>Candidato</th>
                                <th>Telefone</th>
                                <th>Função Pretendida</th>
                                <th>Projeto</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimos_curriculos as $c): ?>
                                <?php 
                                    $iniciais = strtoupper(substr($c['nome'] ?? 'C', 0, 1));
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm me-2 bg-blue-lt fw-bold"><?= $iniciais ?></span>
                                            <div>
                                                <div class="font-weight-medium">
                                                    <a href="/admin/curriculos/visualizar.php?id=<?= (int)$c['id'] ?>" class="text-reset text-decoration-none">
                                                        <?= e($c['nome']) ?>
                                                    </a>
                                                </div>
                                                <div class="text-secondary small">
                                                    <?= $c['data_cadastro'] ? format_date(substr($c['data_cadastro'], 0, 10)) : '—' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-secondary small"><?= e($c['telefone'] ?: '—') ?></td>
                                    <td>
                                        <span class="badge bg-blue-lt"><?= e($c['funcao'] ?: 'Geral') ?></span>
                                    </td>
                                    <td class="text-secondary small text-truncate" style="max-width: 170px;">
                                        <?= e($c['nome_projeto'] ?: 'Geral') ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="/admin/curriculos/visualizar.php?id=<?= (int)$c['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Ver detalhes completos">
                                            Ver Perfil
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Atalhos Rápidos de Ação -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Atalhos Operacionais</h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6 col-sm-3">
                        <a href="/admin/transparencia/painel.php" class="card card-link card-stacked text-center p-3 h-100 text-decoration-none">
                            <div class="text-primary mb-2">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            </div>
                            <div class="font-weight-medium small">Painel de Anexos</div>
                            <div class="text-secondary small" style="font-size: 0.72rem;">Publicar comprovantes</div>
                        </a>
                    </div>
                    <div class="col-6 col-sm-3">
                        <a href="/admin/transparencia/financeiro.php" class="card card-link card-stacked text-center p-3 h-100 text-decoration-none">
                            <div class="text-green mb-2">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            </div>
                            <div class="font-weight-medium small">Prestação Contas</div>
                            <div class="text-secondary small" style="font-size: 0.72rem;">Balanços em PDF</div>
                        </a>
                    </div>
                    <div class="col-6 col-sm-3">
                        <a href="/admin/projetos/criar.php" class="card card-link card-stacked text-center p-3 h-100 text-decoration-none">
                            <div class="text-azure mb-2">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </div>
                            <div class="font-weight-medium small">Novo Projeto</div>
                            <div class="text-secondary small" style="font-size: 0.72rem;">Cadastrar termo/edital</div>
                        </a>
                    </div>
                    <div class="col-6 col-sm-3">
                        <a href="/admin/fornecedores/listar.php" class="card card-link card-stacked text-center p-3 h-100 text-decoration-none">
                            <div class="text-yellow mb-2">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                            </div>
                            <div class="font-weight-medium small">Fornecedores</div>
                            <div class="text-secondary small" style="font-size: 0.72rem;">Empresas e CNPJs</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- COLUNA LATERAL (4 Colunas) -->
    <div class="col-12 col-xl-4">
        <!-- Resumo de Governança & Transparência -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Resumo de Transparência</h3>
                <div class="card-actions">
                    <span class="badge bg-primary-lt"><?= $total_transp_ativos ?> Ativo(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="/admin/transparencia/painel.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <span class="status-dot status-dot-animated bg-green me-2"></span>
                            Painel de Repasses &amp; Documentos
                        </div>
                        <span class="badge bg-secondary-lt"><?= $total_transp_arquivos ?></span>
                    </a>
                    <a href="/admin/transparencia/financeiro.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <span class="status-dot status-dot-animated bg-blue me-2"></span>
                            Demonstrações Financeiras (PDF)
                        </div>
                        <span class="badge bg-secondary-lt"><?= $total_transp_fin ?></span>
                    </a>
                    <a href="/admin/transparencia/termos.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <span class="status-dot status-dot-animated bg-yellow me-2"></span>
                            Termos de Colaboração &amp; Parcerias
                        </div>
                        <span class="badge bg-secondary-lt"><?= $total_transp_termos ?></span>
                    </a>
                </div>
            </div>
            <div class="card-footer bg-transparent py-2 text-center">
                <a href="/admin/transparencia/painel.php" class="small text-secondary text-decoration-none">
                    Gerenciar visibilidade e uploads &rarr;
                </a>
            </div>
        </div>

        <!-- Histórico de Últimos Acessos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Acessos Recentes</h3>
            </div>
            <?php if (empty($ultimos_acessos)): ?>
                <div class="card-body text-center text-secondary py-4 small">
                    Nenhum registro de acesso recente.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-sm">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>IP</th>
                                <th class="text-end">Data / Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimos_acessos as $a): ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-medium small"><?= e($a['usuario']) ?></div>
                                    </td>
                                    <td class="text-secondary small font-monospace" style="font-size: 0.72rem;"><?= e($a['ip'] ?: '127.0.0.1') ?></td>
                                    <td class="text-secondary text-end small" style="font-size: 0.72rem;">
                                        <?= $a['data_hora'] ? date('d/m/y H:i', strtotime($a['data_hora'])) : '—' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
