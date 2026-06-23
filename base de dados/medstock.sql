
// Tabelas de lookup (valores controlados — 3NF)

CREATE TABLE IF NOT EXISTS categorias_equipamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS estados_equipamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS criticidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS tipos_entrada (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS tipos_fornecedor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS tipos_documento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS tipos_contrato (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS periodicidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS perfis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);


// Tabela: localizacao

CREATE TABLE IF NOT EXISTS localizacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    edificio VARCHAR(100),
    piso VARCHAR(20),
    servico VARCHAR(100),
    sala VARCHAR(50),
    localizacao_ativo TINYINT(1) NOT NULL DEFAULT 1
);

// Tabela: equipamento

CREATE TABLE IF NOT EXISTS equipamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_interno VARCHAR(50) UNIQUE,
    nome VARCHAR(150) NOT NULL,
    categoria VARCHAR(100),
    id_categoria INT,
    marca VARCHAR(100),
    modelo VARCHAR(100),
    num_serie VARCHAR(100) UNIQUE,
    fabricante VARCHAR(100),
    data_aquisicao DATE,
    ano_fabrico INT,
    custo DECIMAL(10,2),
    tipo_entrada VARCHAR(50),
    id_tipo_entrada INT,
    estado VARCHAR(50),
    id_estado INT,
    criticidade VARCHAR(20),
    id_criticidade INT,
    observacoes TEXT,
    id_localizacao INT,
    equipamento_ativo TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (id_localizacao) REFERENCES localizacao(id),
    FOREIGN KEY (id_categoria) REFERENCES categorias_equipamento(id),
    FOREIGN KEY (id_estado) REFERENCES estados_equipamento(id),
    FOREIGN KEY (id_criticidade) REFERENCES criticidades(id),
    FOREIGN KEY (id_tipo_entrada) REFERENCES tipos_entrada(id)
);

// Tabela: fornecedor

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
    id_tipo_fornecedor INT,
    observacoes TEXT,
    fornecedor_ativo TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (id_tipo_fornecedor) REFERENCES tipos_fornecedor(id)
);


// Tabela: equipamento_fornecedor

CREATE TABLE IF NOT EXISTS equipamento_fornecedor (
    id_equipamento INT NOT NULL,
    id_fornecedor INT NOT NULL,
    PRIMARY KEY (id_equipamento, id_fornecedor),
    FOREIGN KEY (id_equipamento) REFERENCES equipamento(id),
    FOREIGN KEY (id_fornecedor) REFERENCES fornecedor(id)
);


// Tabela: garantia_contrato

CREATE TABLE IF NOT EXISTS garantia_contrato (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_equipamento INT,
    data_inicio DATE,
    data_fim DATE,
    tem_contrato TINYINT(1) DEFAULT 0,
    tipo_contrato VARCHAR(50),
    id_tipo_contrato INT,
    entidade_responsavel VARCHAR(150),
    periodicidade VARCHAR(50),
    id_periodicidade INT,
    observacoes TEXT,
    garantia_ativo TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (id_equipamento) REFERENCES equipamento(id),
    FOREIGN KEY (id_tipo_contrato) REFERENCES tipos_contrato(id),
    FOREIGN KEY (id_periodicidade) REFERENCES periodicidades(id)
);

//Tabela: documento

CREATE TABLE IF NOT EXISTS documento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_equipamento INT,
    tipo VARCHAR(50),
    id_tipo_documento INT,
    nome VARCHAR(150),
    data_documento DATE,
    data_validade DATE,
    ficheiro VARCHAR(255),
    ficheiro_nome_original VARCHAR(255),
    documento_ativo TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (id_equipamento) REFERENCES equipamento(id),
    FOREIGN KEY (id_tipo_documento) REFERENCES tipos_documento(id)
);

// Tabela: agents (utilizadores do sistema)

CREATE TABLE IF NOT EXISTS agents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARBINARY(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    perfil VARCHAR(50),
    id_perfil INT,
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    agent_ativo TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (id_perfil) REFERENCES perfis(id)
);

//Tabela: gestao_site

CREATE TABLE IF NOT EXISTS gestao_site (
    chave VARCHAR(100) PRIMARY KEY,
    valor TEXT
);

//Tabela: mensagem_contacto

CREATE TABLE IF NOT EXISTS mensagem_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telemovel VARCHAR(20) NOT NULL,
    mensagem TEXT NOT NULL,
    mensagem_lida TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);


//Tabela: logs

CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_evento VARCHAR(50) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    agente_id INT NULL,
    ip VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agente_id) REFERENCES agents(id) ON DELETE SET NULL
);