-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/11/2024 às 12:48
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `workup_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `certificacoes`
--

CREATE TABLE `certificacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `nome` varchar(200) DEFAULT NULL,
  `instituicao` varchar(200) DEFAULT NULL,
  `ano_conclusao` int(11) DEFAULT NULL,
  `link_verificacao` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `certificacoes`
--

INSERT INTO `certificacoes` (`id`, `usuario_id`, `nome`, `instituicao`, `ano_conclusao`, `link_verificacao`, `created_at`) VALUES
(1, 1, 'Muitas', '', 2024, NULL, '2024-11-28 11:43:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `competencias`
--

CREATE TABLE `competencias` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `competencia` varchar(200) DEFAULT NULL,
  `nivel` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `competencias`
--

INSERT INTO `competencias` (`id`, `usuario_id`, `competencia`, `nivel`, `created_at`) VALUES
(1, 1, 'Todas', 'Intermediário', '2024-11-28 11:43:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `escolaridade`
--

CREATE TABLE `escolaridade` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `nivel` varchar(100) DEFAULT NULL,
  `instituicao` varchar(200) DEFAULT NULL,
  `curso` varchar(200) DEFAULT NULL,
  `ano_inicio` int(11) DEFAULT NULL,
  `ano_conclusao` int(11) DEFAULT NULL,
  `em_andamento` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `escolaridade`
--

INSERT INTO `escolaridade` (`id`, `usuario_id`, `nivel`, `instituicao`, `curso`, `ano_inicio`, `ano_conclusao`, `em_andamento`, `created_at`) VALUES
(1, 1, 'Superior completo', '', '', NULL, NULL, 0, '2024-11-28 11:43:17');

-- --------------------------------------------------------

--
-- Estrutura para tabela `idiomas`
--

CREATE TABLE `idiomas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `idioma` varchar(100) DEFAULT NULL,
  `nivel` varchar(50) DEFAULT NULL,
  `certificacao` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `idiomas`
--

INSERT INTO `idiomas` (`id`, `usuario_id`, `idioma`, `nivel`, `certificacao`, `created_at`) VALUES
(1, 1, 'Inglês fluente', 'Básico', NULL, '2024-11-28 11:43:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfis`
--

CREATE TABLE `perfis` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `nome_completo` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `orientacao_sexual` varchar(50) DEFAULT NULL,
  `necessidades_especiais` text DEFAULT NULL,
  `area_atuacao` varchar(200) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `curriculo_pdf` varchar(255) DEFAULT NULL,
  `perfil_completo` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfis`
--

INSERT INTO `perfis` (`id`, `usuario_id`, `nome_completo`, `email`, `telefone`, `endereco`, `numero`, `complemento`, `cep`, `estado`, `cidade`, `data_nascimento`, `genero`, `estado_civil`, `orientacao_sexual`, `necessidades_especiais`, `area_atuacao`, `foto_perfil`, `curriculo_pdf`, `perfil_completo`, `updated_at`) VALUES
(1, 1, 'Ignacio', 'ignacio@gmail.com', '(67) 77777-7777', 'Rua do Delírio', '333', 'Casa', '08320-300', 'SP', 'Santo André', NULL, NULL, NULL, NULL, NULL, NULL, 'imgs/foto_6748579d0c4a1.jpg', 'pdfs/curriculo_674857739fac1.pdf', 1, '2024-11-28 11:44:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `idade` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `idade`, `created_at`) VALUES
(1, 'Ignacio', 'ignacio@gmail.com', '$2y$10$PgZk57aq9dKSGZt9sZjyyO154UBmYhNKhj9i6ZboB9C/H.beQk3g6', 40, '2024-11-28 11:42:09');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `certificacoes`
--
ALTER TABLE `certificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_certificacoes_usuario` (`usuario_id`);

--
-- Índices de tabela `competencias`
--
ALTER TABLE `competencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_competencias_usuario` (`usuario_id`);

--
-- Índices de tabela `escolaridade`
--
ALTER TABLE `escolaridade`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_escolaridade_usuario` (`usuario_id`);

--
-- Índices de tabela `idiomas`
--
ALTER TABLE `idiomas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_idiomas_usuario` (`usuario_id`);

--
-- Índices de tabela `perfis`
--
ALTER TABLE `perfis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_perfil_usuario` (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_usuario_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `certificacoes`
--
ALTER TABLE `certificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `competencias`
--
ALTER TABLE `competencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `escolaridade`
--
ALTER TABLE `escolaridade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `idiomas`
--
ALTER TABLE `idiomas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `perfis`
--
ALTER TABLE `perfis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `certificacoes`
--
ALTER TABLE `certificacoes`
  ADD CONSTRAINT `certificacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `competencias`
--
ALTER TABLE `competencias`
  ADD CONSTRAINT `competencias_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `escolaridade`
--
ALTER TABLE `escolaridade`
  ADD CONSTRAINT `escolaridade_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `idiomas`
--
ALTER TABLE `idiomas`
  ADD CONSTRAINT `idiomas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `perfis`
--
ALTER TABLE `perfis`
  ADD CONSTRAINT `perfis_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
