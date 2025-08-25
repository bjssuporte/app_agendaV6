-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22/08/2025 às 00:02
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
-- Banco de dados: `agenda`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `id_disponibilidade` int(11) NOT NULL,
  `id_servico` int(11) DEFAULT NULL,
  `id_profissional` int(11) DEFAULT NULL,
  `nome_cliente` varchar(160) NOT NULL,
  `telefone_cliente` varchar(20) DEFAULT NULL,
  `email_cliente` varchar(140) NOT NULL,
  `data_hora` datetime NOT NULL,
  `status` enum('confirmado','cancelado','concluido') DEFAULT 'confirmado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `id_disponibilidade`, `id_servico`, `id_profissional`, `nome_cliente`, `telefone_cliente`, `email_cliente`, `data_hora`, `status`) VALUES
(1, 4, 3, 3, 'teste bruno', '81 99999 2222', 'teste@gmail.com', '2025-08-21 20:00:00', 'confirmado'),
(2, 2, 1, 1, 'teste', '71898749879', 'teste@mail.com', '2025-08-21 19:00:00', 'confirmado'),
(3, 7, 4, 3, 'reste 1830', '81897 9879798', 'teste@mail.com', '2025-08-22 15:00:00', 'confirmado'),
(4, 9, 2, 2, 'teste tati', '81 9879874654', 'tati@mail.com', '2025-08-21 21:00:00', 'confirmado');

-- --------------------------------------------------------

--
-- Estrutura para tabela `disponibilidade`
--

CREATE TABLE `disponibilidade` (
  `id` int(11) NOT NULL,
  `profissionalId` int(11) DEFAULT NULL,
  `profissionalNome` varchar(255) NOT NULL,
  `id_servico` int(11) DEFAULT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `status` enum('disponivel','agendado') DEFAULT 'disponivel',
  `clienteNome` varchar(255) DEFAULT NULL,
  `clienteEmail` varchar(255) DEFAULT NULL,
  `clienteTelefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `disponibilidade`
--

INSERT INTO `disponibilidade` (`id`, `profissionalId`, `profissionalNome`, `id_servico`, `data`, `hora`, `status`, `clienteNome`, `clienteEmail`, `clienteTelefone`) VALUES
(1, 1, 'Carlos Machado', 1, '2025-08-21', '18:00:00', 'disponivel', NULL, NULL, NULL),
(2, 1, 'Carlos Machado', 1, '2025-08-21', '19:00:00', 'agendado', 'teste', 'teste@mail.com', '71898749879'),
(3, 3, 'Pamela Almeida', 3, '2025-08-21', '19:00:00', 'disponivel', NULL, NULL, NULL),
(4, 3, 'Pamela Almeida', 3, '2025-08-21', '20:00:00', 'agendado', 'teste bruno', 'teste@gmail.com', '81 99999 2222'),
(5, 3, 'Pamela Almeida', 3, '2025-08-21', '21:00:00', 'disponivel', NULL, NULL, NULL),
(6, 3, 'Pamela Almeida', 4, '2025-08-22', '14:00:00', 'disponivel', NULL, NULL, NULL),
(7, 3, 'Pamela Almeida', 4, '2025-08-22', '15:00:00', 'agendado', 'reste 1830', 'teste@mail.com', '81897 9879798'),
(8, 2, 'Tatiana Brito', 2, '2025-08-21', '20:00:00', 'disponivel', NULL, NULL, NULL),
(9, 2, 'Tatiana Brito', 2, '2025-08-21', '21:00:00', 'agendado', 'teste tati', 'tati@mail.com', '81 9879874654'),
(10, 2, 'Tatiana Brito', 2, '2025-08-21', '22:00:00', 'disponivel', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissionais`
--

CREATE TABLE `profissionais` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `profissionais`
--

INSERT INTO `profissionais` (`id`, `nome`, `email`, `telefone`) VALUES
(1, 'Carlos Machado', 'carlosmteste@mail.com', '81 99880 0000'),
(2, 'Tatiana Brito', 'tatianeb@mail.com', '81 99000 5555'),
(3, 'Pamela Almeida', 'pamela@mail.com', '81 98833 6644');

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissional_servicos`
--

CREATE TABLE `profissional_servicos` (
  `id_profissional` int(11) NOT NULL,
  `id_servico` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `profissional_servicos`
--

INSERT INTO `profissional_servicos` (`id_profissional`, `id_servico`) VALUES
(1, 1),
(2, 2),
(3, 3),
(3, 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `duracao_min` int(11) NOT NULL,
  `preco` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`id`, `nome`, `duracao_min`, `preco`) VALUES
(1, 'Psicoterapia', 45, 150.00),
(2, 'Massoterapia', 50, 180.00),
(3, 'Fono', 50, 200.00),
(4, 'TO', 60, 200.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `created_at`) VALUES
(1, 'Ada Lovelace fake teste', 'ada.lovelace@example.com', '2025-08-18 19:48:03'),
(2, 'Grace Hopper', 'grace.hopper@example.com', '2025-08-18 19:48:03'),
(3, 'Margaret Hamilton', 'margaret.hamilton@example.com', '2025-08-18 19:48:03');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_id_servico_ag` (`id_servico`),
  ADD KEY `idx_id_profissional_ag` (`id_profissional`),
  ADD KEY `idx_id_disponibilidade_ag` (`id_disponibilidade`);

--
-- Índices de tabela `disponibilidade`
--
ALTER TABLE `disponibilidade`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_slot` (`data`,`hora`,`profissionalId`),
  ADD KEY `idx_id_servico_disp` (`id_servico`);

--
-- Índices de tabela `profissionais`
--
ALTER TABLE `profissionais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `profissional_servicos`
--
ALTER TABLE `profissional_servicos`
  ADD PRIMARY KEY (`id_profissional`,`id_servico`),
  ADD KEY `id_servico` (`id_servico`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `disponibilidade`
--
ALTER TABLE `disponibilidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `profissionais`
--
ALTER TABLE `profissionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `fk_ag_disponibilidade` FOREIGN KEY (`id_disponibilidade`) REFERENCES `disponibilidade` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ag_profissional` FOREIGN KEY (`id_profissional`) REFERENCES `profissionais` (`id`),
  ADD CONSTRAINT `fk_ag_servico` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id`);

--
-- Restrições para tabelas `disponibilidade`
--
ALTER TABLE `disponibilidade`
  ADD CONSTRAINT `fk_disp_servico` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id`);

--
-- Restrições para tabelas `profissional_servicos`
--
ALTER TABLE `profissional_servicos`
  ADD CONSTRAINT `fk_ps_profissional` FOREIGN KEY (`id_profissional`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ps_servico` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
