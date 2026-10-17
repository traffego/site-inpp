<?php
// templates/admin/sidebar.php — Barra Lateral Vertical do Tabler
if (!defined('ROOT_PATH')) exit;

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';

function is_admin_active(string $path, string $current_uri): bool {
    if ($path === '/admin' || $path === '/admin/dashboard.php') {
        return $current_uri === '/admin' || $current_uri === '/admin/' || $current_uri === '/admin/index.php' || $current_uri === '/admin/dashboard.php';
    }
    return str_starts_with($current_uri, $path);
}
?>
<aside class="navbar navbar-vertical navbar-expand-lg">
    <div class="container-fluid">
        <!-- Botão Toggle Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Marca Institucional INPP -->
        <div class="navbar-brand navbar-brand-autodark px-2 py-3 text-start">
            <a href="/admin/dashboard.php" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="/assets/img/logo_inpp.png" alt="INPP" class="navbar-brand-image" style="height: 38px; width: auto; max-width: 100%; object-fit: contain;">
            </a>
        </div>

        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                
                <!-- SEÇÃO: PRINCIPAL -->
                <li class="nav-item">
                    <span class="nav-link-title text-uppercase text-muted fw-bold px-3 py-1 d-block" style="font-size: 0.68rem; letter-spacing: 0.08em; opacity: 0.75;">Principal</span>
                </li>
                
                <li class="nav-item <?= is_admin_active('/admin', $uri) && !str_contains($uri, 'projetos') && !str_contains($uri, 'noticias') && !str_contains($uri, 'curriculos') && !str_contains($uri, 'fornecedores') && !str_contains($uri, 'transparencia') && !str_contains($uri, 'configuracoes') ? 'active' : '' ?>">
                    <a class="nav-link" href="/admin/dashboard.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item <?= is_admin_active('/admin/projetos', $uri) ? 'active' : '' ?>">
                    <a class="nav-link" href="/admin/projetos/listar.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        </span>
                        <span class="nav-link-title">Projetos</span>
                    </a>
                </li>

                <li class="nav-item <?= is_admin_active('/admin/noticias', $uri) ? 'active' : '' ?>">
                    <a class="nav-link" href="/admin/noticias/listar.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </span>
                        <span class="nav-link-title">Notícias</span>
                    </a>
                </li>

                <li class="nav-item <?= is_admin_active('/admin/curriculos', $uri) ? 'active' : '' ?>">
                    <a class="nav-link" href="/admin/curriculos/listar.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </span>
                        <span class="nav-link-title">Currículos</span>
                    </a>
                </li>

                <li class="nav-item <?= is_admin_active('/admin/fornecedores', $uri) ? 'active' : '' ?>">
                    <a class="nav-link" href="/admin/fornecedores/listar.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                        </span>
                        <span class="nav-link-title">Fornecedores</span>
                    </a>
                </li>

                <!-- SEÇÃO: TRANSPARÊNCIA -->
                <li class="nav-item mt-3">
                    <span class="nav-link-title text-uppercase text-muted fw-bold px-3 py-1 d-block" style="font-size: 0.68rem; letter-spacing: 0.08em; opacity: 0.75;">Transparência</span>
                </li>

                <li class="nav-item <?= is_admin_active('/admin/transparencia/painel.php', $uri) ? 'active' : '' ?>">
                    <a class="nav-link" href="/admin/transparencia/painel.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        </span>
                        <span class="nav-link-title">Painel de Arquivos</span>
                    </a>
                </li>

                <li class="nav-item <?= is_admin_active('/admin/transparencia/financeiro.php', $uri) ? 'active' : '' ?>">
                    <a class="nav-link" href="/admin/transparencia/financeiro.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        </span>
                        <span class="nav-link-title">Doc. Financeiros</span>
                    </a>
                </li>

                <li class="nav-item <?= is_admin_active('/admin/transparencia/termos.php', $uri) ? 'active' : '' ?>">
                    <a class="nav-link" href="/admin/transparencia/termos.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        </span>
                        <span class="nav-link-title">Termos e Parcerias</span>
                    </a>
                </li>

                <!-- SEÇÃO: SISTEMA -->
                <li class="nav-item mt-3">
                    <span class="nav-link-title text-uppercase text-muted fw-bold px-3 py-1 d-block" style="font-size: 0.68rem; letter-spacing: 0.08em; opacity: 0.75;">Sistema</span>
                </li>

                <li class="nav-item <?= is_admin_active('/admin/configuracoes', $uri) ? 'active' : '' ?>">
                    <a class="nav-link" href="/admin/configuracoes/index.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        </span>
                        <span class="nav-link-title">Configurações</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-danger" href="/admin/logout.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block text-danger">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </span>
                        <span class="nav-link-title">Sair do Painel</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
