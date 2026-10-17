-- 005_seed_curriculos_fornecedores.sql
-- Importação de currículos e fornecedores do banco legado

-- Legado: data_cadastro = int(8) formato YYYYMMDD → converter para created_at
-- Colunas novas em tab_curriculos: telefone_1, telefone_2, e_mail, etc.
-- Colunas legadas na ordem: id, nome, telefone_2, e_mail, endereco, bairro, cidade, estado, id_funcao, data_cadastro, sexo, data_nascimento, telefone_1, estado_civil, cep, idioma_sim_nao, idioma_quais, informatica_sim_nao, escolaridade, cursos_relevantes, experiencia_sim_nao, empresa_1, empresa_1_periodo, empresa_1_funcao, empresa_2, empresa_2_periodo, empresa_2_funcao, empresa_3, empresa_3_periodo, empresa_3_funcao, experiencia_profissional, id_projeto

INSERT IGNORE INTO `tab_curriculos` (`id`, `nome`, `telefone_2`, `e_mail`, `endereco`, `bairro`, `cidade`, `estado`, `id_funcao`, `telefone_1`, `sexo`, `data_nascimento`, `estado_civil`, `cep`, `idioma_sim_nao`, `idioma_quais`, `informatica_sim_nao`, `escolaridade`, `cursos_relevantes`, `experiencia_sim_nao`, `empresa_1`, `empresa_1_periodo`, `empresa_1_funcao`, `empresa_2`, `empresa_2_periodo`, `empresa_2_funcao`, `empresa_3`, `empresa_3_periodo`, `empresa_3_funcao`, `experiencia_profissional`, `id_projeto`, `arquivo_curriculo`) VALUES
(1,'Jairo Tavares da Silva','','','','','','',14,'98236-9761','','','','',0,'',0,'','',0,'','','','','','','','','','',7, '1.pdf'),
(2,'Jose Maurício Soares de Araújo','','','','','','',20,'99329-006','','','','',0,'',0,'','',0,'','','','','','','','','','',7, '2.pdf'),
(3,'José Mauricio Soares de Araujo','','','','','','',20,'99932-9006','','','','',0,'',0,'','',0,'','','','','','','','','','',7, '3.pdf'),
(4,'Mauro José Gomes da Silva','','','','','','',20,'99873-0640','','','','',0,'',0,'','',0,'','','','','','','','','','',7, '4.pdf'),
(5,'Mauro José Gomes da Silva','','','','','','',20,'99873-0640','','','','',0,'',0,'','',0,'','','','','','','','','','',7, '5.pdf'),
(6,'RICARDO ROBERTO BARRETO DA ROCHA FILHO','','','','','','',6,'99996-9924','','','','',0,'',0,'','',0,'','','','','','','','','','',10, '6.pdf'),
(7,'RICARDO ROBERTO BARRETO DA ROCHA FILHO','','','','','','',6,'99996-9924','','','','',0,'',0,'','',0,'','','','','','','','','','',1, '7.pdf'),
(8,'RICARDO ROBERTO BARRETO DA ROCHA FILHO','','','','','','',6,'99996-9924','','','','',0,'',0,'','',0,'','','','','','','','','','',13, '8.pdf'),
(9,'RICARDO ROBERTO BARRETO DA ROCHA FILHO','','','','','','',6,'99996-9924','','','','',0,'',0,'','',0,'','','','','','','','','','',12, '9.pdf'),
(10,'RICARDO ROBERTO BARRETO DA ROCHA FILHO','','','','','','',6,'99996-9924','','','','',0,'',0,'','',0,'','','','','','','','','','',11, '10.pdf'),
(11,'RICARDO ROBERTO BARRETO DA ROCHA FILHO','','','','','','',6,'99996-9924','','','','',0,'',0,'','',0,'','','','','','','','','','',8, '11.pdf'),
(12,'RICARDO ROBERTO BARRETO DA ROCHA FILHO','','','','','','',6,'99996-9924','','','','',0,'',0,'','',0,'','','','','','','','','','',9, '12.pdf'),
(13,'RICARDO ROBERTO BARRETO DA ROCHA FILHO','','','','','','',6,'99996-9924','','','','',0,'',0,'','',0,'','','','','','','','','','',7, '13.pdf'),
(14,'RICARDO ROBERTO BARRETO DA ROCHA FILHO','','','','','','',6,'99996-9924','','','','',0,'',0,'','',0,'','','','','','','','','','',6, '14.pdf'),
(15,'MARIA LILLIAM FERREIRA DOS SANTOS','','','','','','',2,'99866-7468','','','','',0,'',0,'','',0,'','','','','','','','','','',10, '15.pdf'),
(16,'GENECI ANTONIO DA SILVA JUNIOR','','','','','','',8,'99845-2640','','','','',0,'',0,'','',0,'','','','','','','','','','',12, '16.pdf'),
(17,'EDNALDO CANDIDO DE SOUZA','','','','','','',21,'99319-9976','','','','',0,'',0,'','',0,'','','','','','','','','','',7, '17.pdf'),
(18,'EDNALDO CANDIDO DE SOUZA','','','','','','',8,'99319-9976','','','','',0,'',0,'','',0,'','','','','','','','','','',6, '18.pdf'),
(19,'Aline Quezia Acioly de Lima Souza','','','','','','',6,'99704-9997','','','','',0,'',0,'','',0,'','','','','','','','','','',12, '19.pdf'),
(20,'Ana Claudia Leandro da Silva','','','','','','',8,'99544-8100','','','','',0,'',0,'','',0,'','','','','','','','','','',7, '20.pdf'),
(21,'Ana Claudia Leandro da Silva','','','','','','',8,'99544-8100','','','','',0,'',0,'','',0,'','','','','','','','','','',7, '21.pdf'),
(22,'Ana Claudia Leandro da Silva','','','','','','',8,'99544-8100','','','','',0,'',0,'','',0,'','','','','','','','','','',7, '22.pdf'),
(25,'EDNALDO CANDIDO DE SOUZA','','','','','','',5,'99319-9976','','','','',0,'',0,'','',0,'','','','','','','','','','',12, '25.pdf'),
(26,'RAFAEL MERISIO DE FILTRO','','','','','','',14,'99989-6564','','','','',0,'',0,'','',0,'','','','','','','','','','',12, '26.pdf'),
(27,'Jairo Tavares da Silva','','','','','','',3,'98236-9761','','','','',0,'',0,'','',0,'','','','','','','','','','',12, '27.pdf'),
(28,'Ana Claudia Leandro da Silva Lima','','','','','','',7,'99544-8100','','','','',0,'',0,'','',0,'','','','','','','','','','',10, '28.pdf'),
(29,'MARIA LILLIAM FERREIRA DOS SANTOS','','','','','','',2,'99866-7468','','','','',0,'',0,'','',0,'','','','','','','','','','',9, '29.pdf'),
(30,'MARIA LILLIAM FERREIRA DOS SANTOS','','','','','','',2,'98667-468','','','','',0,'',0,'','',0,'','','','','','','','','','',8, '30.pdf'),
(31,'MARIA LILLIAM FERREIRA DOS SANTOS','','','','','','',2,'98667-468','','','','',0,'',0,'','',0,'','','','','','','','','','',12, '31.pdf'),
(32,'MARIA LILLIAM FERREIRA DOS SANTOS','','','','','','',2,'99866-7468','','','','',0,'',0,'','',0,'','','','','','','','','','',12, '32.pdf'),
(33,'Ana Claudia Leandro da Silva','','','','','','',7,'99544-8100','','','','',0,'',0,'','',0,'','','','','','','','','','',11, '33.pdf'),
(34,'Ana Claudia Leandro da Silva','','','','','','',7,'99544-8100','','','','',0,'',0,'','',0,'','','','','','','','','','',12, '34.pdf'),
(35,'EDNALDO CANDIDO DE SOUZA','','','','','','',5,'99319-9976','','','','',0,'',0,'','',0,'','','','','','','','','','',11, '35.pdf'),
(36,'MARTA CRISTINA ALVES LEITE','','','','','','',4,'99643-0350','','','','',0,'',0,'','',0,'','','','','','','','','','',11, '36.pdf'),
(37,'MARTA CRISTINA ALVES LEITE','','','','','','',4,'99643-0350','','','','',0,'',0,'','',0,'','','','','','','','','','',11, '37.pdf'),
(38,'MARTA CRISTINA ALVES LEITE','','','','','','',4,'99643-0350','','','','',0,'',0,'','',0,'','','','','','','','','','',12, '38.pdf'),
(39,'GENECI ANTONIO DA SILVA JUNIOR','','','','','','',8,'99845-2640','','','','',0,'',0,'','',0,'','','','','','','','','','',11, '39.pdf'),
(40,'Jairo Tavares da Silva','','','','','','',3,'98236-9761','','','','',0,'',0,'','',0,'','','','','','','','','','',11, '40.pdf'),
(41,'Jairo Tavares da Silva','','','','','','',3,'98236-9761','','','','',0,'',0,'','',0,'','','','','','','','','','',1, '41.pdf'),
(42,'RAFAEL MERISIO DE FILTRO','','','','','','',1,'99989-6564','','','','',0,'',0,'','',0,'','','','','','','','','','',11, '42.pdf'),
(43,'MARIA LILLIAM FERREIRA DOS SANTOS','','','','','','',2,'99866-7468','','','','',0,'',0,'','',0,'','','','','','','','','','',11, '43.pdf'),
(44,'EDNALDO CANDIDO DE SOUZA','','','','','','',5,'99319-9976','','','','',0,'',0,'','',0,'','','','','','','','','','',1, '44.pdf'),
(45,'EDNALDO CANDIDO DE SOUZA','','','','','','',5,'99319-9976','','','','',0,'',0,'','',0,'','','','','','','','','','',13, '45.pdf'),
(46,'MARTA CRISTINA ALVES LEITE','','','','','','',4,'99643-0350','','','','',0,'',0,'','',0,'','','','','','','','','','',1, '46.pdf'),
(47,'MARTA CRISTINA ALVES LEITE','','','','','','',4,'99643-0350','','','','',0,'',0,'','',0,'','','','','','','','','','',13, '47.pdf'),
(48,'GENECI ANTONIO DA SILVA JUNIOR','','','','','','',8,'99845-2640','','','','',0,'',0,'','',0,'','','','','','','','','','',13, '48.pdf'),
(49,'GENECI ANTONIO DA SILVA JUNIOR','','','','','','',8,'99845-2640','','','','',0,'',0,'','',0,'','','','','','','','','','',1, '49.pdf'),
(50,'Jairo Tavares da Silva','','','','','','',3,'98236-9761','','','','',0,'',0,'','',0,'','','','','','','','','','',13, '50.pdf'),
(51,'Ana Claudia Leandro da Silva','','','','','','',7,'99544-8100','','','','',0,'',0,'','',0,'','','','','','','','','','',13, '51.pdf'),
(52,'Ana Claudia Leandro da Silva','','','','','','',7,'99544-8100','','','','',0,'',0,'','',0,'','','','','','','','','','',1, '52.pdf'),
(53,'Ana Claudia Leandro da Silva','','','','','','',7,'99544-8100','','','','',0,'',0,'','',0,'','','','','','','','','','',1, '53.pdf'),
(54,'RAFAEL MERISIO DE FILTRO','','','','','','',1,'99989-6564','','','','',0,'',0,'','',0,'','','','','','','','','','',1, '54.pdf'),
(55,'Maria Lilliam Ferreira','','','','','','',2,'99866-7468','','','','',0,'',0,'','',0,'','','','','','','','','','',1, '55.pdf'),
(56,'MARIA LILLIAM FERREIRA DOS SANTOS','','','','','','',2,'99866-7468','','','','',0,'',0,'','',0,'','','','','','','','','','',11, '56.pdf'),
(57,'MARIA LILLIAM FERREIRA DOS SANTOS','','','','','','',2,'99866-7468','','','','',0,'',0,'','',0,'','','','','','','','','','',13, '57.pdf');

UPDATE `tab_curriculos` SET `arquivo_curriculo` = CONCAT(id, '.pdf') WHERE `arquivo_curriculo` IS NULL OR `arquivo_curriculo` = '';

-- Fornecedores (único registro do banco legado estava praticamente vazio - importar assim mesmo)
INSERT IGNORE INTO `tab_fornecedores` (`id`, `nome_fantasia`, `razao_social`, `cnpj`, `endereco`, `bairro`, `cidade`, `estado`, `cep`, `contato_nome`, `contato_cargo`, `contato_telefone`, `contato_email`) VALUES
(1, '', '', '', '', '', '', '', '', '', ' ', '', '');
