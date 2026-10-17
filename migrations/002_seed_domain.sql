-- migrations/002_seed_domain.sql

SET NAMES utf8mb4;

-- Inserir estados
INSERT INTO `estado` (`idUf`, `nome`) VALUES
('AC', 'Acre'), ('AL', 'Alagoas'), ('AM', 'Amazonas'), ('AP', 'Amapá'),
('BA', 'Bahia'), ('CE', 'Ceará'), ('DF', 'Distrito Federal'), ('ES', 'Espírito Santo'),
('GO', 'Goiás'), ('MA', 'Maranhão'), ('MG', 'Minas Gerais'), ('MS', 'Mato Grosso do Sul'),
('MT', 'Mato Grosso'), ('PA', 'Pará'), ('PB', 'Paraíba'), ('PE', 'Pernambuco'),
('PI', 'Piauí'), ('PR', 'Paraná'), ('RJ', 'Rio de Janeiro'), ('RN', 'Rio Grande do Norte'),
('RO', 'Rondônia'), ('RR', 'Roraima'), ('RS', 'Rio Grande do Sul'), ('SC', 'Santa Catarina'),
('SE', 'Sergipe'), ('SP', 'São Paulo'), ('TO', 'Tocantins');

-- NOTA: As cidades (5593 registros) devem ser importadas separadamente

-- Status de Projetos
INSERT INTO `tab_projetos_status` (`projetos_status`) VALUES
('Concluído'),
('Em Execução'),
('Em Prestação de Contas'),
('Assinado'),
('Proposta Aprovada e Plano de Trabalho Complementado em Análise');

-- Tipos de Documentos
INSERT INTO `tab_projetos_documentos_tipo` (`tipo_documento`) VALUES
('Editais'),
('Homologações');

-- Funções Curriculares (22 funções herdadas do sistema legado)
INSERT INTO `tab_curriculos_funcao` (`funcao`, `ativo`) VALUES
('Administrador', 1), ('Assistente Social', 1), ('Auxiliar Administrativo', 1),
('Auxiliar de Limpeza', 1), ('Auxiliar de Serviços Gerais', 1), ('Coordenador', 1),
('Coordenador Geral', 1), ('Coordenador Pedagógico', 1), ('Coordenador Técnico', 1),
('Educador Físico', 1), ('Enfermeiro', 1), ('Estagiário', 1), ('Fisioterapeuta', 1),
('Fonoaudiólogo', 1), ('Instrutor', 1), ('Monitor', 1), ('Motorista', 1),
('Nutricionista', 1), ('Pedagogo', 1), ('Professor', 1), ('Psicólogo', 1), ('Técnico de Enfermagem', 1);

-- Organização (cod_org=10001)
INSERT INTO `tab_org` (`cod_org`, `nome_org`, `cnpj`, `endereco`, `bairro`, `cidade`, `estado`, `telefone`, `e_mail`, `liberado`) VALUES
(10001, 'INSTITUTO ATLETAS PARA SEMPRE', '17.397.521/0001-27', 'Rua Eugênio de Lima, 143, sala 01', 'Boa Viagem', 'Recife', 'PE', '(81) 98328-8465', 'gestao.atletaparasempre@gmail.com', 1);

-- Admin user (senha pre-hasheada para 'admin123' usando bcrypt)
INSERT INTO `tab_login` (`usuario`, `senha`, `nome_usuario`, `nivel`, `cod_org`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', '1', 10001);
