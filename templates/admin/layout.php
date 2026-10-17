<?php
// templates/admin/layout.php — Layout Mestre do Admin com Tabler UI
if (!defined('ROOT_PATH')) exit;

$page_title = $page_title ?? 'Admin — Instituto Nacional de Políticas Públicas';
?>
<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    
    <!-- Tabler Core CSS -->
    <link href="/assets/vendor/tabler/tabler.min.css" rel="stylesheet"/>
    <link href="/assets/vendor/tabler/tabler-icons.min.css" rel="stylesheet"/>
    
    <!-- CSS Customizado com Cache-Busting -->
    <?php
    $css_v = file_exists(ROOT_PATH . '/assets/css/admin.css') ? filemtime(ROOT_PATH . '/assets/css/admin.css') : time();
    ?>
    <link rel="stylesheet" href="/assets/css/admin.css?v=<?= $css_v ?>">
    
    <!-- TinyMCE -->
    <?php if (!empty(TINYMCE_API_KEY)): ?>
    <script src="https://cdn.tiny.cloud/1/<?= e(TINYMCE_API_KEY) ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <?php else: ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <?php endif; ?>
    
    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">

    <!-- Anti-flicker Script de Tema do Admin -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('tabler_theme') || localStorage.getItem('admin_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
</head>
<body class="layout-fluid">
    <div class="page">
        <!-- Sidebar -->
        <?php include ROOT_PATH . '/templates/admin/sidebar.php'; ?>
        
        <!-- Header -->
        <?php include ROOT_PATH . '/templates/admin/header.php'; ?>

        <div class="page-wrapper">
            <!-- Main Content Body -->
            <div class="page-body">
                <div class="container-xl">
                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash_type'] ?? 'info') ?> alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div><?= htmlspecialchars($_SESSION['flash_message']) ?></div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                        <?php 
                            unset($_SESSION['flash_message']); 
                            unset($_SESSION['flash_type']);
                        ?>
                    <?php endif; ?>

                    <?php if (isset($content)) echo $content; ?>
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="footer footer-transparent d-print-none py-3 border-top">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    <a href="/" target="_blank" class="link-secondary">Portal Público</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="/admin/configuracoes" class="link-secondary">Configurações</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0 text-muted">
                                <li class="list-inline-item">
                                    &copy; <?= date('Y') ?> <strong class="text-reset">Instituto Nacional de Políticas Públicas (INPP)</strong> &bull; Painel de Gestão
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Tabler Core JS -->
    <script src="/assets/vendor/tabler/tabler.min.js" defer></script>
    <script>
        // Alternância de Tema Admin
        function toggleAdminTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('tabler_theme', newTheme);
            localStorage.setItem('admin_theme', newTheme);
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Inicializar TinyMCE se presente
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '.tinymce-editor',
                    menubar: false,
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                    toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                    content_style: 'body { font-family: "Inter", sans-serif; font-size:15px; }'
                });
            }
        });
    </script>
    <?php if (isset($extra_scripts)) echo $extra_scripts; ?>
</body>
</html>
