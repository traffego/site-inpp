-- 008_seed_projeto_panelas_27668.sql
-- Inserção do Projeto 027668/2026 (Torneio de Futebol 3ª Edição em Panelas/PE), Grupos e Editais

-- 1. Inserir Projeto (ID 17)
INSERT IGNORE INTO `tab_projetos` (`id`, `nome_projeto`, `mostra_inicial`, `ativo`, `num_proposta`, `termo_fomento`, `valor`, `projeto_status`, `objeto`, `apresentacao`) 
VALUES (17, '027668/2026 - EVENTO TORNEIO DE FUTEBOL 3ª EDIÇÃO EM PANELAS/PE', 1, 1, '027668/2026', '027668/2026', 'A definir', 2, 'Realização do Evento Torneio de Futebol 3ª Edição em Panelas no Estado de Pernambuco', 'Evento Torneio de Futebol 3ª Edição em Panelas');

-- 2. Inserir Grupos de Documentos (IDs 107 a 113)
INSERT IGNORE INTO `tab_projetos_grupo_doc` (`id`, `id_projeto`, `nome_grupo`, `posicao`) VALUES
(107, 17, '001 - Recursos Humanos', 1),
(108, 17, '002 - Materiais Esportivos', 2),
(109, 17, '003 - Aquisição de Uniformes', 3),
(110, 17, '004 - Produção de Eventos', 4),
(111, 17, '005 - Assessoria Técnica', 5),
(112, 17, '006 - Assessoria de Comunicação', 6),
(113, 17, '007 - Identificação e Divulgação', 7);

-- 3. Inserir Documentos/Editais (IDs 141 a 147)
INSERT IGNORE INTO `tab_projetos_documentos` (`id`, `id_projeto`, `id_grupo_documento`, `id_tipo_documento`, `nome_documento`, `arquivo`) VALUES
(141, 17, 107, 1, '001 - Edital Recursos Humanos', '141.pdf'),
(142, 17, 108, 1, '002 - Edital Material Esportivo', '142.pdf'),
(143, 17, 109, 1, '003 - Edital Aquisição de Uniformes', '143.pdf'),
(144, 17, 110, 1, '004 - Edital Produção de Eventos', '144.pdf'),
(145, 17, 111, 1, '005 - Edital Assessoria Técnica', '145.pdf'),
(146, 17, 112, 1, '006 - Edital Assessoria de Comunicação', '146.pdf'),
(147, 17, 113, 1, '007 - Edital Identificação e Divulgação', '147.pdf');
