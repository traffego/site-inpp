-- migrations/001_schema.sql

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. estado
CREATE TABLE `estado` (
  `idUf` char(2) NOT NULL,
  `nome` varchar(30) NOT NULL,
  PRIMARY KEY (`idUf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- NOTA: Tabela 'cidade' removida.
-- Cidades são carregadas dinamicamente via API IBGE:
-- https://servicodados.ibge.gov.br/api/v1/localidades/estados/{UF}/municipios
-- O valor armazenado nos cadastros (tab_curriculos.cidade, tab_fornecedores.cidade) é o NOME da cidade como texto.


-- 3. tab_org
CREATE TABLE `tab_org` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `cod_org` INT NOT NULL,
  `nome_org` varchar(100) DEFAULT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `presidente_org` varchar(50) DEFAULT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `e_mail` varchar(40) DEFAULT NULL,
  `endereco` varchar(80) DEFAULT NULL,
  `bairro` varchar(40) DEFAULT NULL,
  `cidade` varchar(70) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `site` varchar(100) DEFAULT NULL,
  `ordem` varchar(4) DEFAULT NULL,
  `liberado` TINYINT DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_org_cod_org` (`cod_org`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. tab_login
CREATE TABLE `tab_login` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome_usuario` varchar(100) DEFAULT NULL,
  `nivel` varchar(2) DEFAULT NULL,
  `cod_org` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_login_usuario` (`usuario`),
  KEY `idx_login_cod_org` (`cod_org`),
  CONSTRAINT `fk_login_org` FOREIGN KEY (`cod_org`) REFERENCES `tab_org` (`cod_org`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. tab_login_erro
CREATE TABLE `tab_login_erro` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `cod_org` INT DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_login_erro_cod_org` (`cod_org`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. tab_login_registro
CREATE TABLE `tab_login_registro` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `usuario` INT DEFAULT NULL,
  `dia_acesso` varchar(2) DEFAULT NULL,
  `mes_acesso` varchar(2) DEFAULT NULL,
  `ano_acesso` varchar(4) DEFAULT NULL,
  `hora_acesso` varchar(8) DEFAULT NULL,
  `cod_org` INT DEFAULT NULL,
  `ip` varchar(100) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_login_registro_usuario` (`usuario`),
  CONSTRAINT `fk_login_registro_usuario` FOREIGN KEY (`usuario`) REFERENCES `tab_login` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. tab_projetos_status
CREATE TABLE `tab_projetos_status` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `projetos_status` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. tab_projetos
CREATE TABLE `tab_projetos` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `nome_projeto` varchar(250) NOT NULL,
  `imagem` varchar(50) DEFAULT NULL,
  `mostra_inicial` TINYINT DEFAULT 0,
  `cadastrado_por` INT DEFAULT NULL,
  `data_cadastro` DATE DEFAULT NULL,
  `hora_cadastro` TIME DEFAULT NULL,
  `ultima_alteracao` INT DEFAULT NULL,
  `data_ultima_alteracao` DATE DEFAULT NULL,
  `hora_ultima_alteracao` TIME DEFAULT NULL,
  `ativo` TINYINT DEFAULT 1,
  `num_proposta` varchar(50) DEFAULT NULL,
  `termo_fomento` varchar(50) DEFAULT NULL,
  `valor` varchar(30) DEFAULT NULL,
  `data_assinatura` DATE NULL,
  `publicacao_dou` DATE NULL,
  `inicio_vigencia` DATE NULL,
  `termino_vigencia` DATE NULL,
  `prestacao_contas` DATE NULL,
  `projeto_status` INT DEFAULT NULL,
  `objeto` TEXT DEFAULT NULL,
  `apresentacao` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_projetos_status` (`projeto_status`),
  CONSTRAINT `fk_projetos_status` FOREIGN KEY (`projeto_status`) REFERENCES `tab_projetos_status` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. tab_projetos_categorias
CREATE TABLE `tab_projetos_categorias` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `nome_categoria` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. tab_projetos_grupo_doc
CREATE TABLE `tab_projetos_grupo_doc` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `id_projeto` INT DEFAULT NULL,
  `nome_grupo` varchar(200) NOT NULL,
  `posicao` INT DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_grupo_doc_projeto` (`id_projeto`),
  CONSTRAINT `fk_grupo_doc_projeto` FOREIGN KEY (`id_projeto`) REFERENCES `tab_projetos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. tab_projetos_documentos_tipo
CREATE TABLE `tab_projetos_documentos_tipo` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `tipo_documento` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. tab_projetos_documentos
CREATE TABLE `tab_projetos_documentos` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `id_projeto` INT DEFAULT NULL,
  `id_grupo_documento` INT DEFAULT NULL,
  `id_tipo_documento` INT DEFAULT NULL,
  `nome_documento` varchar(200) NOT NULL,
  `arquivo` varchar(100) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_doc_projeto` (`id_projeto`),
  KEY `idx_doc_grupo` (`id_grupo_documento`),
  KEY `idx_doc_tipo` (`id_tipo_documento`),
  CONSTRAINT `fk_doc_projeto` FOREIGN KEY (`id_projeto`) REFERENCES `tab_projetos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_doc_grupo` FOREIGN KEY (`id_grupo_documento`) REFERENCES `tab_projetos_grupo_doc` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_doc_tipo` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tab_projetos_documentos_tipo` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. tab_curriculos_funcao
CREATE TABLE `tab_curriculos_funcao` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `funcao` varchar(150) NOT NULL,
  `ativo` TINYINT DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. tab_projetos_funcao
CREATE TABLE `tab_projetos_funcao` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `id_projeto` INT DEFAULT NULL,
  `id_funcao` INT DEFAULT NULL,
  `cadastrado_por` INT DEFAULT NULL,
  `data_cadastro` DATE DEFAULT NULL,
  `hora_cadastro` TIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_proj_func_projeto` (`id_projeto`),
  KEY `idx_proj_func_funcao` (`id_funcao`),
  CONSTRAINT `fk_proj_func_projeto` FOREIGN KEY (`id_projeto`) REFERENCES `tab_projetos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_proj_func_funcao` FOREIGN KEY (`id_funcao`) REFERENCES `tab_curriculos_funcao` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. tab_curriculos
CREATE TABLE `tab_curriculos` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `nome` varchar(200) NOT NULL,
  `telefone_1` varchar(15) DEFAULT NULL,
  `telefone_2` varchar(30) DEFAULT NULL,
  `e_mail` varchar(100) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `bairro` varchar(50) DEFAULT NULL,
  `cidade` varchar(70) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `id_funcao` INT DEFAULT NULL,
  `id_projeto` INT DEFAULT NULL,
  `sexo` varchar(15) DEFAULT NULL,
  `data_nascimento` varchar(10) DEFAULT NULL,
  `estado_civil` varchar(30) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `idioma_sim_nao` TINYINT DEFAULT 0,
  `idioma_quais` varchar(100) DEFAULT NULL,
  `informatica_sim_nao` TINYINT DEFAULT 0,
  `escolaridade` varchar(100) DEFAULT NULL,
  `cursos_relevantes` TEXT DEFAULT NULL,
  `experiencia_sim_nao` TINYINT DEFAULT 0,
  `empresa_1` varchar(100) DEFAULT NULL,
  `empresa_1_periodo` varchar(30) DEFAULT NULL,
  `empresa_1_funcao` varchar(100) DEFAULT NULL,
  `empresa_2` varchar(100) DEFAULT NULL,
  `empresa_2_periodo` varchar(30) DEFAULT NULL,
  `empresa_2_funcao` varchar(100) DEFAULT NULL,
  `empresa_3` varchar(100) DEFAULT NULL,
  `empresa_3_periodo` varchar(30) DEFAULT NULL,
  `empresa_3_funcao` varchar(100) DEFAULT NULL,
  `experiencia_profissional` TEXT DEFAULT NULL,
  `arquivo_curriculo` varchar(100) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_curriculo_funcao` (`id_funcao`),
  KEY `idx_curriculo_projeto` (`id_projeto`),
  CONSTRAINT `fk_curriculo_funcao` FOREIGN KEY (`id_funcao`) REFERENCES `tab_curriculos_funcao` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_curriculo_projeto` FOREIGN KEY (`id_projeto`) REFERENCES `tab_projetos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. tab_fornecedores
CREATE TABLE `tab_fornecedores` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `nome_fantasia` varchar(200) NOT NULL,
  `razao_social` varchar(200) NOT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `bairro` varchar(70) DEFAULT NULL,
  `cidade` varchar(70) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `contato_nome` varchar(200) DEFAULT NULL,
  `contato_cargo` varchar(50) DEFAULT NULL,
  `contato_telefone` varchar(15) DEFAULT NULL,
  `contato_email` varchar(100) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. tab_noticias
CREATE TABLE `tab_noticias` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `manchete` varchar(250) NOT NULL,
  `resumo` varchar(250) DEFAULT NULL,
  `noticia` TEXT DEFAULT NULL,
  `data_noticia` DATE DEFAULT NULL,
  `n_visitas` INT DEFAULT 0,
  `imagem_inicio` varchar(100) DEFAULT NULL,
  `imagem_final` varchar(100) DEFAULT NULL,
  `cod_org` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_noticias_org` (`cod_org`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. tab_dirigente
CREATE TABLE `tab_dirigente` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `nome_diretor` varchar(100) NOT NULL,
  `cargo_diretor` varchar(50) DEFAULT NULL,
  `posicao` INT DEFAULT NULL,
  `cod_org` INT DEFAULT NULL,
  `telefone` varchar(100) DEFAULT NULL,
  `e_mail` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_dirigente_org` (`cod_org`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. tab_documentos_financeiro
CREATE TABLE `tab_documentos_financeiro` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `data_documento` DATE DEFAULT NULL,
  `titulo` varchar(250) NOT NULL,
  `resumo` varchar(250) DEFAULT NULL,
  `arquivo` varchar(100) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. tab_documentos_termo_colaboracao
CREATE TABLE `tab_documentos_termo_colaboracao` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `data_documento` DATE DEFAULT NULL,
  `titulo` varchar(250) NOT NULL,
  `resumo` varchar(250) DEFAULT NULL,
  `arquivo` varchar(100) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. tab_eventos
CREATE TABLE `tab_eventos` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `evento` varchar(100) NOT NULL,
  `data_evento` DATE DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22. tab_fotos_album
CREATE TABLE `tab_fotos_album` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `nome_album` varchar(100) NOT NULL,
  `data_criacao` DATE DEFAULT NULL,
  `cod_org` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fotos_album_org` (`cod_org`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 23. tab_fotos_2
CREATE TABLE `tab_fotos_2` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `cod_album` INT DEFAULT NULL,
  `foto` varchar(100) NOT NULL,
  `legenda` varchar(200) DEFAULT NULL,
  `prioridade` INT DEFAULT 0,
  `cod_org` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fotos2_album` (`cod_album`),
  KEY `idx_fotos2_org` (`cod_org`),
  CONSTRAINT `fk_fotos2_album` FOREIGN KEY (`cod_album`) REFERENCES `tab_fotos_album` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
