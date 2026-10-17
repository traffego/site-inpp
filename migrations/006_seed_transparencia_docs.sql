-- 006_seed_transparencia_docs.sql
-- Inserir documentos institucionais e financeiros legados na transparência

INSERT IGNORE INTO `tab_documentos_termo_colaboracao` (`id`, `data_documento`, `titulo`, `resumo`, `arquivo`) VALUES
(1, '2024-01-15', 'Estatuto Social do Instituto', 'Estatuto social consolidado e registrado em cartório', 'estatuto.pdf'),
(2, '2024-01-15', 'Declaração da Diretoria e Dirigentes', 'Relação e qualificação completa dos dirigentes da instituição', 'dirigentes.pdf'),
(3, '2024-01-15', 'Regulamento de Compras e Contratações', 'Normas internas para cotações, compras e contratações de serviços', 'regulamento_compras.pdf');

INSERT IGNORE INTO `tab_documentos_financeiro` (`id`, `data_documento`, `titulo`, `resumo`, `arquivo`) VALUES
(1, '2025-12-18', 'Painel de Transferências Legais e Discricionárias - Dez/2025', 'Relatório detalhado das transferências de recursos públicos recebidos', '2025_12_18_painel_de_transferencias_legais_e_discricionarias.xlsx'),
(2, '2025-12-30', 'Painel de Transferências Legais e Discricionárias - Dez/2025 (Atualizado)', 'Demonstrativo atualizado de repasses e despesas atreladas a convênios', '2025_12_30_painel_de_transferencias_legais_e_discricionarias.xlsx');
