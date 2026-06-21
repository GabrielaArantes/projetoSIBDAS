-- ============================================================
-- MedStock — Estrutura da Base de Dados
-- Projeto SIBDAS — Gestão de Inventário de Equipamento Hospitalar
-- Base de dados: db1241094
-- ============================================================

-- ------------------------------------------------------------
-- Tabela: localizacao
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS localizacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    edificio VARCHAR(50) NOT NULL,
    piso VARCHAR(50),
    servico VARCHAR(100),
    sala VARCHAR(50),
    localizacao_ativo TINYINT(1) NOT NULL DEFAULT 1
);

-- ------------------------------------------------------------
-- Tabela: equipamento
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS equipamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_interno VARCHAR(50),
    nome VARCHAR(150) NOT NULL,
    categoria VARCHAR(100),
    marca VARCHAR(100),
    modelo VARCHAR(100),
    num_serie VARCHAR(100),
    fabricante VARCHAR(100),
    data_aquisicao DATE,
    ano_fabrico INT,
    custo DECIMAL(10,2),
    tipo_entrada VARCHAR(50),
    estado VARCHAR(50),
    criticidade VARCHAR(20),
    observacoes TEXT,
    id_localizacao INT,
    equipamento_ativo TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (id_localizacao) REFERENCES localizacao(id)
);

-- ------------------------------------------------------------
-- Tabela: fornecedor
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS fornecedor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    nif VARCHAR(20),
    telefone VARCHAR(20),
    email VARCHAR(100),
    morada VARCHAR(200),
    website VARCHAR(150),
    pessoa_contacto VARCHAR(100),
    telefone_contacto VARCHAR(20),
    tipo VARCHAR(50),
    observacoes TEXT,
    fornecedor_ativo TINYINT(1) NOT NULL DEFAULT 1
);

-- ------------------------------------------------------------
-- Tabela: equipamento_fornecedor (associação muitos-para-muitos)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS equipamento_fornecedor (
    id_equipamento INT NOT NULL,
    id_fornecedor INT NOT NULL,
    PRIMARY KEY (id_equipamento, id_fornecedor),
    FOREIGN KEY (id_equipamento) REFERENCES equipamento(id),
    FOREIGN KEY (id_fornecedor) REFERENCES fornecedor(id)
);

-- ------------------------------------------------------------
-- Tabela: garantia_contrato
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS garantia_contrato (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_equipamento INT,
    data_inicio DATE,
    data_fim DATE,
    tipo_contrato VARCHAR(50),
    entidade_responsavel VARCHAR(150),
    periodicidade VARCHAR(50),
    observacoes TEXT,
    garantia_ativo TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (id_equipamento) REFERENCES equipamento(id)
);

-- ------------------------------------------------------------
-- Tabela: documento
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS documento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_equipamento INT,
    tipo VARCHAR(50),
    nome VARCHAR(150),
    data_documento DATE,
    data_validade DATE,
    ficheiro VARCHAR(255),
    ficheiro_nome_original VARCHAR(255),
    documento_ativo TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (id_equipamento) REFERENCES equipamento(id)
);

-- ------------------------------------------------------------
-- Tabela: agents (utilizadores do sistema / login)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS agents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARBINARY(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    perfil ENUM('Administrador', 'Técnico', 'Profissional de Saude') NOT NULL,
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    agent_ativo TINYINT(1) NOT NULL DEFAULT 1
);

-- ------------------------------------------------------------
-- Tabela: gestao_site (conteúdos da página pública)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS gestao_site (
    chave VARCHAR(100) PRIMARY KEY,
    valor TEXT
);
