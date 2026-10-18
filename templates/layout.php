<?php
/**
 * Layout base do site público
 * Instituto Atleta Para Sempre
 * 
 * Variáveis esperadas:
 * - $page_title (string)
 * - $page_description (string) 
 * - $content (string - HTML capturado via ob_start/ob_get_clean)
 */

$site_name = 'Instituto Nacional de Políticas Públicas';
$full_title = isset($page_title) ? e($page_title) . ' | ' . $site_name : $site_name;
$meta_desc = $page_description ?? 'Instituto Nacional de Políticas Públicas - INPP.';

// Tema padrão configurado na administração (padrão institucional INPP = light)
$default_theme = 'light';
try {
    if (function_exists('db_fetch')) {
        $org_theme = db_fetch('SELECT tema_padrao FROM tab_org WHERE cod_org = 10001 LIMIT 1');
        if (!empty($org_theme['tema_padrao'])) {
            $default_theme = $org_theme['tema_padrao'];
        }
    }
} catch (Throwable $e) {}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($meta_desc) ?>">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#0A7ABF">
    <title><?= $full_title ?></title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Phosphor Icons CDN -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- GLightbox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/css/glightbox.min.css">

    <!-- CSS Principal (Glacier Design) -->
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">

    <!-- Garantir padrão claro oficial INPP -->
    <script>
        try { localStorage.removeItem('theme'); } catch(e) {}
    </script>
</head>
<body class="light">
    <?php include ROOT_PATH . '/templates/header.php'; ?>

    <!-- Fixed Flash Toast Messages -->
    <div class="flash-container">
        <?php if ($flash_success = flash('success')): ?>
            <div class="flash-message flash-success" role="alert">
                <i data-lucide="check-circle-2"></i>
                <div>
                    <strong>Sucesso!</strong>
                    <p><?= e($flash_success) ?></p>
                </div>
                <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Fechar">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($flash_error = flash('error')): ?>
            <div class="flash-message flash-error" role="alert">
                <i data-lucide="alert-triangle"></i>
                <div>
                    <strong>Atenção!</strong>
                    <p><?= e($flash_error) ?></p>
                </div>
                <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Fechar">&times;</button>
            </div>
        <?php endif; ?>
    </div>

    <main id="main-content">
        <?= $content ?? '' ?>
    </main>

    <?php include ROOT_PATH . '/templates/footer.php'; ?>

    <!-- Back to Top -->
    <button id="back-to-top" class="back-to-top" aria-label="Voltar ao topo" title="Voltar ao topo">
        <i data-lucide="chevron-up"></i>
    </button>

    <!-- GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/js/glightbox.min.js"></script>

    <!-- Main JS -->
    <script src="<?= asset('assets/js/main.js') ?>"></script>

    <script>
        // Lucide Icons init
        function initIcons() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        }
        initIcons();

        // GLightbox init
        if (typeof GLightbox !== 'undefined') {
            GLightbox({ selector: '.glightbox' });
        }

        // Flash messages auto-dismiss
        document.querySelectorAll('.flash-message').forEach(el => {
            setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 5000);
        });
    </script>
</body>
</html>
