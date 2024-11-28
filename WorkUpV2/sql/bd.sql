-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS workup_db;
USE workup_db;

-- Tabela de usuários (informações básicas de login)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    idade INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de perfis (informações detalhadas)
CREATE TABLE IF NOT EXISTS perfis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT UNIQUE,
    nome_completo VARCHAR(100),
    email VARCHAR(100),
    telefone VARCHAR(20),
    endereco VARCHAR(200),
    numero VARCHAR(20),
    complemento VARCHAR(100),
    cep VARCHAR(10),
    estado VARCHAR(2),
    cidade VARCHAR(100),
    data_nascimento DATE,
    genero VARCHAR(50),
    estado_civil VARCHAR(50),
    orientacao_sexual VARCHAR(50),
    necessidades_especiais TEXT,
    area_atuacao VARCHAR(200),
    foto_perfil VARCHAR(255),
    curriculo_pdf VARCHAR(255),
    perfil_completo BOOLEAN DEFAULT FALSE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de escolaridade
CREATE TABLE IF NOT EXISTS escolaridade (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    nivel VARCHAR(100),
    instituicao VARCHAR(200),
    curso VARCHAR(200),
    ano_inicio INT,
    ano_conclusao INT,
    em_andamento BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de competências
CREATE TABLE IF NOT EXISTS competencias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    competencia VARCHAR(200),
    nivel VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de certificações
CREATE TABLE IF NOT EXISTS certificacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    nome VARCHAR(200),
    instituicao VARCHAR(200),
    ano_conclusao INT,
    link_verificacao VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de idiomas
CREATE TABLE IF NOT EXISTS idiomas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    idioma VARCHAR(100),
    nivel VARCHAR(50),
    certificacao VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Índices para otimização
CREATE INDEX idx_usuario_email ON usuarios(email);
CREATE INDEX idx_perfil_usuario ON perfis(usuario_id);
CREATE INDEX idx_escolaridade_usuario ON escolaridade(usuario_id);
CREATE INDEX idx_competencias_usuario ON competencias(usuario_id);
CREATE INDEX idx_certificacoes_usuario ON certificacoes(usuario_id);
CREATE INDEX idx_idiomas_usuario ON idiomas(usuario_id);

-- Configurações de charset
ALTER DATABASE workup_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE usuarios CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE perfis CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE escolaridade CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE competencias CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE certificacoes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE idiomas CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;