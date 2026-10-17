-- 007_seed_homologacoes_992680_992681_992669.sql
-- Inserção das Homologações de Cotação Prévia de Serviços para os projetos 992680, 992681 e 992669

-- Documento 138: Homologação CT Prévia - Projeto 15 (992680 - Torneio Futebol Masc/Fem Ed.1 Salgueiro/PE)
-- Documento 139: Homologação CT Prévia - Projeto 14 (992681 - Torneio Futebol Masc/Fem Ed.2 Bezerros/PE)
-- Documento 140: Homologação CT Prévia - Projeto 16 (992669 - Escolinha Futebol/Futsal Vitória de Santo Antão/PE)

INSERT IGNORE INTO `tab_projetos_documentos` (`id`, `id_projeto`, `id_grupo_documento`, `id_tipo_documento`, `nome_documento`, `arquivo`) VALUES 
(138, 15, 102, 2, '002 Cotação Prévia de Preços - Homologação', '138.pdf'),
(139, 14, 106, 2, '002 Cotação Prévia de Preços - Homologação', '139.pdf'),
(140, 16, 98, 2, '002 Cotação Prévia de Preços - Homologação', '140.pdf');
