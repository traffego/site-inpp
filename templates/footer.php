<?php
// templates/footer.php
if (!defined('ROOT_PATH')) exit;

$footer_org = null;
try {
    if (function_exists('db_fetch')) {
        $footer_org = db_fetch('SELECT * FROM tab_org WHERE cod_org = 10001 LIMIT 1');
    }
} catch (Throwable $e) {}

$cnpj = $footer_org['cnpj'] ?? '27.282.557/0001-19';
$telefone = $footer_org['telefone'] ?? '(81) 98328-8465';
$email = $footer_org['e_mail'] ?? 'gestao.atletaparasempre@gmail.com';

$endereco_completo = 'Av. Ayrton Senna, nº 3.000 – sala 321 bloco 1 – Barra da Tijuca, Rio de Janeiro - RJ, CEP 22.775-904';
if (!empty($footer_org['endereco'])) {
    $endereco_completo = $footer_org['endereco'];
    if (!empty($footer_org['bairro'])) $endereco_completo .= ' – ' . $footer_org['bairro'];
    if (!empty($footer_org['cidade']) && !empty($footer_org['estado'])) {
        $endereco_completo .= ', ' . $footer_org['cidade'] . ' - ' . $footer_org['estado'];
    }
    if (!empty($footer_org['cep'])) $endereco_completo .= ', CEP ' . $footer_org['cep'];
}

$tel_num = preg_replace('/\D/', '', $telefone);
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Coluna 1: Institucional -->
            <div class="footer-col-brand">
                <a href="/" class="footer-logo-link" title="Instituto Nacional de Políticas Públicas">
                    <img src="/assets/img/logo_inpp.png" alt="INPP" class="footer-logo">
                </a>
                <p class="footer-about">
                    Organização da sociedade civil dedicada ao desenvolvimento de iniciativas e políticas públicas com foco em cidadania, educação e inclusão social.
                </p>
                <div class="footer-social">
                    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <i class="ph ph-facebook-logo"></i>
                    </a>
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <i class="ph ph-instagram-logo"></i>
                    </a>
                    <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                        <i class="ph ph-youtube-logo"></i>
                    </a>
                    <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                        <i class="ph ph-linkedin-logo"></i>
                    </a>
                </div>
            </div>

            <!-- Coluna 2: Acesso Institucional -->
            <div class="footer-col">
                <h4 class="footer-heading">Institucional</h4>
                <ul class="footer-nav">
                    <li><a href="/quem-somos">Quem Somos</a></li>
                    <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('projetos')): ?>
                    <li><a href="/projetos">Projetos & Ações</a></li>
                    <?php endif; ?>
                    <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('editais')): ?>
                    <li><a href="/editais">Editais & Seleções</a></li>
                    <?php endif; ?>
                    <li><a href="/trabalhe-conosco">Trabalhe Conosco</a></li>
                    <li><a href="/fornecedores">Fornecedores</a></li>
                </ul>
            </div>

            <!-- Coluna 3: Transparência -->
            <?php if (function_exists('is_transparencia_habilitada') && is_transparencia_habilitada('transparencia')): ?>
            <div class="footer-col">
                <h4 class="footer-heading">Transparência</h4>
                <ul class="footer-nav">
                    <li><a href="/transparencia/declaracao">Declaração de Transparência</a></li>
                    <?php if (is_transparencia_habilitada('estatuto')): ?>
                    <li><a href="/transparencia/estatuto">Estatuto Social</a></li>
                    <?php endif; ?>
                    <?php if (is_transparencia_habilitada('dirigentes')): ?>
                    <li><a href="/transparencia/dirigentes">Dirigentes</a></li>
                    <?php endif; ?>
                    <?php if (is_transparencia_habilitada('financeiro')): ?>
                    <li><a href="/transparencia/financeiro">Demonstrações Financeiras</a></li>
                    <?php endif; ?>
                    <?php if (is_transparencia_habilitada('termos')): ?>
                    <li><a href="/transparencia/termos">Termos de Parceria</a></li>
                    <?php endif; ?>
                    <?php if (is_transparencia_habilitada('painel')): ?>
                    <li><a href="/transparencia/painel">Painel de Transferências</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Coluna 4: Contato & Sede -->
            <div class="footer-col">
                <h4 class="footer-heading">Sede & Atendimento</h4>
                <div class="footer-contact-info">
                    <p><strong>CNPJ:</strong> <?= e($cnpj) ?></p>
                    <p><strong>Endereço:</strong> <?= e($endereco_completo) ?></p>
                    <p><strong>Telefone:</strong> <a href="tel:<?= e($tel_num) ?>"><?= e($telefone) ?></a></p>
                    <p><strong>E-mail:</strong> <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>Copyright &copy; <?= date('Y') ?> Instituto Nacional de Políticas Públicas. Todos os direitos reservados.</p>
            <p class="footer-mrosc-note">Atuação em conformidade com o Marco Regulatório das OSCs (Lei 13.019/2014)</p>
        </div>
    </div>
</footer>
