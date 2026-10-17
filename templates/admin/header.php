<?php
// templates/admin/header.php — Topbar Horizontal do Tabler
if (!defined('ROOT_PATH')) exit;

$user_name = $_SESSION['admin_nome'] ?? 'Administrador';
$words = explode(' ', trim($user_name));
$initials = strtoupper(substr($words[0] ?? 'A', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
?>
<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none border-bottom">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Trilha de Navegação (Breadcrumb) -->
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div>
                <ol class="breadcrumb breadcrumb-arrows mb-0" aria-label="breadcrumbs">
                    <li class="breadcrumb-item"><a href="/admin/dashboard.php">Admin</a></li>
                    <?php if (!empty($breadcrumb) && is_array($breadcrumb)): ?>
                        <?php foreach ($breadcrumb as $item): ?>
                            <?php if (!empty($item['url'])): ?>
                                <li class="breadcrumb-item"><a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a></li>
                            <?php else: ?>
                                <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0)"><?= e($item['label']) ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php elseif (isset($breadcrumb_title)): ?>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0)"><?= htmlspecialchars($breadcrumb_title) ?></a></li>
                    <?php endif; ?>
                </ol>
            </div>
        </div>

        <!-- Ações do Header (Direita) -->
        <div class="navbar-nav flex-row order-md-last align-items-center gap-2">
            <!-- Alternador de Tema Claro / Escuro -->
            <a href="javascript:void(0)" onclick="toggleAdminTheme()" class="nav-link px-2" title="Alternar Modo Claro / Escuro" data-bs-toggle="tooltip" data-bs-placement="bottom">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            </a>

            <!-- Link para o Portal Público -->
            <a href="/" target="_blank" class="btn btn-outline-secondary btn-sm d-none d-sm-inline-flex align-items-center gap-1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                <span>Ver Portal</span>
            </a>

            <!-- Dropdown do Usuário -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Abrir menu do usuário">
                    <span class="avatar avatar-sm bg-primary-lt fw-bold"><?= htmlspecialchars($initials) ?></span>
                    <div class="d-none d-xl-block ps-2">
                        <div class="fw-medium text-start"><?= htmlspecialchars($user_name) ?></div>
                        <div class="mt-1 small text-muted text-start" style="font-size: 0.72rem;">Gestor do Sistema</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="/admin/configuracoes/index.php" class="dropdown-item">Configurações &amp; Perfil</a>
                    <div class="dropdown-divider"></div>
                    <a href="/admin/logout.php" class="dropdown-item text-danger">Sair do Painel</a>
                </div>
            </div>
        </div>
    </div>
</header>
