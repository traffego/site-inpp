<?php
/**
 * Admin: Excluir Notícia (POST only)
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(base_url('/admin/noticias/listar.php')); }
csrf_verify();

$id = (int)($_POST['id'] ?? 0);
if ($id) {
    $noticia = db_fetch('SELECT imagem_inicio, imagem_final FROM tab_noticias WHERE id = ?', [$id]);
    if ($noticia) {
        if ($noticia['imagem_inicio']) @unlink(UPLOAD_PATH . '/noticias/' . $noticia['imagem_inicio']);
        if ($noticia['imagem_final'])  @unlink(UPLOAD_PATH . '/noticias/' . $noticia['imagem_final']);
        db_delete('tab_noticias', 'id = ?', [$id]);
        flash('success', 'Notícia excluída com sucesso.');
    }
}

redirect(base_url('/admin/noticias/listar.php'));
