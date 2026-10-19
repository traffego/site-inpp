<?php
/**
 * Editais e Documentos — Instituto Nacional de Políticas Públicas
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Editais e Documentos';
$page_description = 'Consulte os editais, homologações e documentos dos projetos do Instituto Nacional de Políticas Públicas (INPP).';

// Filtro por projeto
$filtro_projeto = (int)($_GET['projeto'] ?? 0);
$busca          = trim($_GET['busca'] ?? '');

// Condições de busca (apenas projetos com documentos ativos)
$where = 'WHERE p.id IN (SELECT DISTINCT id_projeto FROM tab_projetos_documentos WHERE habilitado = 1)';
$params = [];

if ($filtro_projeto > 0) {
    $where .= ' AND p.id = ?';
    $params[] = $filtro_projeto;
}

if (!empty($busca)) {
    $where .= ' AND (p.nome_projeto LIKE ? OR p.num_proposta LIKE ?)';
    $params[] = '%' . $busca . '%';
    $params[] = '%' . $busca . '%';
}

// Buscar projetos
$projetos = db_fetch_all(
    "SELECT p.*, ps.projetos_status as status_nome
     FROM tab_projetos p
     LEFT JOIN tab_projetos_status ps ON p.projeto_status = ps.id
     $where
     ORDER BY p.id DESC",
    $params
);

// Apenas projetos que possuem documentos habilitados para o dropdown de filtro
$todos_projetos = db_fetch_all(
    'SELECT p.id, p.nome_projeto, p.num_proposta 
     FROM tab_projetos p 
     WHERE p.id IN (SELECT DISTINCT id_projeto FROM tab_projetos_documentos WHERE habilitado = 1)
     ORDER BY p.id DESC'
);

$status_class = [
    'Concluído'   => 'badge-success',
    'Em Execução' => 'badge-primary',
    'Em Prestação de Contas' => 'badge-warning',
    'Assinado'    => 'badge-info',
];

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <span>Editais e Documentos</span>
        </nav>
        <h1 class="page-hero-title"><i data-lucide="file-check" style="color:var(--color-primary);"></i> Editais & Transparência</h1>
        <p class="page-hero-sub">Consulte editais, homologações e documentos de prestação de contas dos projetos do Instituto.</p>
    </div>
</section>

<section class="section" id="editais">
    <div class="container">

        <!-- BARRA DE FILTRO E PESQUISA -->
        <div class="card fade-in-up" style="margin-bottom: 2.5rem; padding: 1.5rem;">
            <form method="GET" action="/editais" id="form-filtro">
                <div class="form-row" style="margin-bottom: 0;">
                    <div class="form-group" style="margin-bottom: 0; flex: 1;">
                        <label for="projeto" class="form-label"><i data-lucide="filter"></i> Selecionar Projeto</label>
                        <select name="projeto" id="projeto" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos os projetos disponíveis</option>
                            <?php foreach ($todos_projetos as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $filtro_projeto === (int)$p['id'] ? 'selected' : '' ?>>
                                <?= e($p['nome_projeto']) ?> <?= $p['num_proposta'] ? '(Nº ' . e($p['num_proposta']) . ')' : '' ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0; flex: 1;">
                        <label for="busca" class="form-label"><i data-lucide="search"></i> Buscar por Termo</label>
                        <div style="display:flex; gap:0.5rem;">
                            <input type="text" name="busca" id="busca" class="form-input" placeholder="Digite nome ou número do edital..." value="<?= e($busca) ?>">
                            <button type="submit" class="btn btn-primary"><i data-lucide="search"></i> Buscar</button>
                        </div>
                    </div>
                </div>

                <?php if ($filtro_projeto || !empty($busca)): ?>
                <div style="margin-top: 1rem; display:flex; align-items:center; justify-content:space-between;">
                    <span style="font-size:0.8rem; color:var(--text-muted);">
                        Filtro ativo: <?= $filtro_projeto ? 'Projeto selecionado' : '' ?> <?= !empty($busca) ? 'Termo "' . e($busca) . '"' : '' ?>
                    </span>
                    <a href="/editais" class="btn btn-ghost btn-sm"><i data-lucide="x"></i> Limpar Filtros</a>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- LISTA DE PROJETOS COM DOCUMENTOS -->
        <?php if (empty($projetos)): ?>
        <div class="card text-center fade-in-up" style="padding: 3rem;">
            <div style="width:56px; height:56px; margin:0 auto 1rem auto; border-radius:50%; background:var(--color-primary-alpha); color:var(--color-primary); display:flex; align-items:center; justify-content:center;">
                <i data-lucide="file-x" style="width:28px; height:28px;"></i>
            </div>
            <h3>Nenhum edital encontrado</h3>
            <p style="color:var(--text-muted); max-width:450px; margin:0.5rem auto 1.5rem auto;">Nenhum documento ou projeto corresponde aos critérios de pesquisa selecionados.</p>
            <a href="/editais" class="btn btn-ghost"><i data-lucide="rotate-ccw"></i> Ver Todos os Editais</a>
        </div>
        <?php else: ?>

        <div class="editais-list fade-in-up" style="display:flex; flex-direction:column; gap:2rem;">
            <?php foreach ($projetos as $projeto): ?>
            <?php
                // Buscar todos os documentos vinculados diretamente ao projeto
                $todos_docs = db_fetch_all(
                    'SELECT d.*, t.tipo_documento, g.nome_grupo
                     FROM tab_projetos_documentos d
                     LEFT JOIN tab_projetos_documentos_tipo t ON d.id_tipo_documento = t.id
                     LEFT JOIN tab_projetos_grupo_doc g ON d.id_grupo_documento = g.id
                     WHERE d.id_projeto = ? AND d.habilitado = 1
                     ORDER BY d.id_grupo_documento, d.id',
                    [$projeto['id']]
                );

                // Grupos de documentos deste projeto
                $grupos = db_fetch_all(
                    'SELECT * FROM tab_projetos_grupo_doc WHERE id_projeto = ? ORDER BY posicao, id',
                    [$projeto['id']]
                );
            ?>
            <div class="card projeto-card-item" id="projeto-<?= $projeto['id'] ?>" style="margin-bottom:0;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem; border-bottom:1px dashed var(--border-color); padding-bottom:1rem;">
                    <div>
                        <span class="badge badge-primary" style="margin-bottom:0.5rem;"><i data-lucide="folder"></i> Projeto Esportivo</span>
                        <h2 style="font-size:1.3rem; font-weight:800; color:var(--text-main); margin-bottom:0.25rem;">
                            <?= e($projeto['nome_projeto']) ?>
                        </h2>
                        <?php if ($projeto['num_proposta']): ?>
                        <div style="font-size:0.8rem; font-family:var(--font-mono); color:var(--text-muted);">
                            Proposta / Processo Nº <?= e($projeto['num_proposta']) ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <?php if ($projeto['status_nome']): ?>
                        <span class="badge <?= e($status_class[$projeto['status_nome']] ?? 'badge-secondary') ?>">
                            <?= e($projeto['status_nome']) ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($projeto['valor']): ?>
                        <span style="font-weight:800; font-family:var(--font-mono); color:var(--color-primary);">
                            <?= e($projeto['valor']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($projeto['objeto']): ?>
                <p style="font-size:0.875rem; color:var(--text-muted); margin-bottom:1.5rem; line-height:1.6;">
                    <?= e($projeto['objeto']) ?>
                </p>
                <?php endif; ?>

                <!-- EXIBIÇÃO DOS DOCUMENTOS E EDITAIS -->
                <?php if (empty($todos_docs)): ?>
                <div style="background:var(--color-primary-alpha); padding:1rem; border-radius:var(--radius-md); font-size:0.825rem; color:var(--text-muted); display:flex; align-items:center; gap:0.5rem;">
                    <i data-lucide="info"></i> Documentação em fase de catalogação ou atualização.
                </div>
                <?php else: ?>
                    
                    <?php 
                    // Agrupar documentos por grupo (ou por padrão)
                    $docs_por_grupo = [];
                    foreach ($todos_docs as $doc) {
                        $gname = !empty($doc['nome_grupo']) ? $doc['nome_grupo'] : 'Editais & Documentos Gerais';
                        $docs_por_grupo[$gname][] = $doc;
                    }
                    ?>

                    <div style="display:flex; flex-direction:column; gap:1.25rem;">
                        <?php foreach ($docs_por_grupo as $nome_grupo => $lista_docs): ?>
                        <div style="background:var(--bg-surface-hover); border:1px solid var(--border-color); border-radius:var(--radius-md); padding:1.25rem;">
                            <h4 style="font-size:0.9rem; font-weight:800; color:var(--text-main); margin-bottom:1rem; display:flex; align-items:center; gap:0.4rem;">
                                <i data-lucide="layers" style="color:var(--color-primary);"></i> <?= e($nome_grupo) ?>
                            </h4>

                            <ul style="list-style:none; display:flex; flex-direction:column; gap:0.75rem;">
                                <?php foreach ($lista_docs as $doc): ?>
                                <li style="display:flex; align-items:center; justify-content:space-between; padding:0.6rem 0.85rem; background:var(--bg-surface); border:1px solid var(--border-color); border-radius:var(--radius-sm); flex-wrap:wrap; gap:0.75rem;">
                                    <div style="display:flex; align-items:center; gap:0.6rem; flex:1; min-width:200px;">
                                        <i data-lucide="file-text" style="color:var(--color-primary);"></i>
                                        <span style="font-size:0.85rem; font-weight:600; color:var(--text-main);">
                                            <?= e($doc['nome_documento']) ?>
                                        </span>
                                        <?php if (!empty($doc['tipo_documento'])): ?>
                                        <span class="badge badge-sm <?= strtolower($doc['tipo_documento']) === 'editais' ? 'badge-primary' : 'badge-success' ?>">
                                            <?= e($doc['tipo_documento']) ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php 
                                        $nome_arquivo_doc = !empty($doc['arquivo']) ? $doc['arquivo'] : ((int)$doc['id'] . '.pdf');
                                    ?>
                                    <a href="/uploads/projetos/<?= e($nome_arquivo_doc) ?>"
                                       class="btn btn-primary btn-sm"
                                       target="_blank"
                                       rel="noopener"
                                       aria-label="Baixar <?= e($doc['nome_documento']) ?>">
                                        <i data-lucide="download"></i> Baixar PDF
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>

            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
