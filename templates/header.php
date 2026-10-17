<?php
// templates/header.php
if (!defined('ROOT_PATH')) exit;

$total_noticias = 0;
try {
    if (function_exists('db_count')) {
        $total_noticias = db_count('SELECT COUNT(*) FROM tab_noticias');
    }
} catch (Throwable $e) {
    $total_noticias = 0;
}
?>
<header class="site-header">
    <div class="container">
        <nav class="navbar">
            <a href="/" class="logo-inverted-container" title="Instituto Nacional de Políticas Públicas">
                <img src="/assets/img/logo_inpp.png" alt="Instituto Nacional de Políticas Públicas" class="header-logo">
            </a>

            <div class="navbar-collapse" id="mobile-menu">
                <ul class="navbar-nav">
                    <li class="nav-item"><a href="/quem-somos" class="nav-link"><i class="ph ph-info"></i> Quem Somos</a></li>
                    
                    <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('projetos')): ?>
                    <li class="nav-item"><a href="/projetos" class="nav-link"><i class="ph ph-trophy"></i> Projetos</a></li>
                    <?php endif; ?>
                    
                    <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('noticias')): ?>
                    <li class="nav-item"><a href="/noticias" class="nav-link"><i class="ph ph-newspaper"></i> Notícias</a></li>
                    <?php endif; ?>

                    <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('transparencia')): ?>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" id="navbarDropdown">
                            <i class="ph-duotone ph-shield-check"></i> Transparência
                            <i class="ph ph-caret-down" style="font-size:12px; margin-left:2px;"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="/transparencia/declaracao" class="dropdown-item"><i class="ph ph-file-text"></i> Declaração</a>
                            <?php if (is_transparencia_habilitada('dirigentes')): ?>
                            <a href="/transparencia/dirigentes" class="dropdown-item"><i class="ph ph-users"></i> Dirigentes</a>
                            <?php endif; ?>
                            <?php if (is_transparencia_habilitada('estatuto')): ?>
                            <a href="/transparencia/estatuto" class="dropdown-item"><i class="ph ph-file-check"></i> Estatuto</a>
                            <?php endif; ?>
                            <?php if (is_transparencia_habilitada('financeiro')): ?>
                            <a href="/transparencia/financeiro" class="dropdown-item"><i class="ph ph-currency-circle-dollar"></i> Financeiro</a>
                            <?php endif; ?>
                            <?php if (is_transparencia_habilitada('regulamento')): ?>
                            <a href="/transparencia/regulamento" class="dropdown-item"><i class="ph ph-book-open"></i> Regulamento</a>
                            <?php endif; ?>
                            <?php if (is_transparencia_habilitada('termos')): ?>
                            <a href="/transparencia/termos" class="dropdown-item"><i class="ph ph-signature"></i> Termos</a>
                            <?php endif; ?>
                            <?php if (is_transparencia_habilitada('painel')): ?>
                            <a href="/transparencia/painel" class="dropdown-item"><i class="ph ph-chart-line-up"></i> Painel de Transferências</a>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item"><a href="/trabalhe-conosco" class="nav-link"><i class="ph ph-briefcase"></i> Trabalhe Conosco</a></li>
                    <li class="nav-item"><a href="/fornecedores" class="nav-link"><i class="ph ph-buildings"></i> Fornecedores</a></li>
                    <li class="nav-item"><a href="/contato" class="nav-link"><i class="ph ph-envelope-simple"></i> Contato</a></li>
                </ul>
            </div>

            <div class="navbar-actions">
                <button id="mobile-menu-toggle" class="navbar-toggler btn-icon" aria-label="Menu">
                    <i class="ph ph-list"></i>
                </button>
            </div>
        </nav>
    </div>
</header>
