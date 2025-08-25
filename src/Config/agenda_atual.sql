-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2025 at 09:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
SET
    SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
    time_zone = "+00:00";

--
-- Database: `agenda`
--
-- --------------------------------------------------------
--
-- Table structure for table `profissionais`
--
CREATE TABLE
    `profissionais` (
        `id` int (11) NOT NULL,
        `nome` varchar(255) NOT NULL,
        `email` varchar(255) DEFAULT NULL,
        `telefone` varchar(20) DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `servicos`
--
CREATE TABLE
    `servicos` (
        `id` int (11) NOT NULL,
        `nome` varchar(255) NOT NULL,
        `duracao_min` int (11) NOT NULL,
        `preco` decimal(10, 2) DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `profissional_servicos`
--
CREATE TABLE
    `profissional_servicos` (
        `id_profissional` int (11) NOT NULL,
        `id_servico` int (11) NOT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `disponibilidade`
--
CREATE TABLE
    `disponibilidade` (
        `id` int (11) NOT NULL,
        `profissionalId` int (11) DEFAULT NULL,
        `profissionalNome` varchar(255) NOT NULL,
        `id_servico` int (11) DEFAULT NULL,
        `data` date NOT NULL,
        `hora` time NOT NULL,
        `status` enum ('disponivel', 'agendado') DEFAULT 'disponivel',
        `clienteNome` varchar(255) DEFAULT NULL,
        `clienteEmail` varchar(255) DEFAULT NULL,
        `clienteTelefone` varchar(20) DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `agendamentos`
--
CREATE TABLE
    `agendamentos` (
        `id` int (11) NOT NULL,
        `id_disponibilidade` int (11) NOT NULL,
        `id_servico` int (11) DEFAULT NULL,
        `id_profissional` int (11) DEFAULT NULL,
        `nome_cliente` varchar(160) NOT NULL,
        `telefone_cliente` varchar(20) DEFAULT NULL,
        `email_cliente` varchar(140) NOT NULL,
        `data_hora` datetime NOT NULL,
        `status` enum ('confirmado', 'cancelado', 'concluido') DEFAULT 'confirmado'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--                  INDEXES AND PRIMARY KEYS
-- --------------------------------------------------------
--
-- Indexes for table `profissionais`
--
ALTER TABLE `profissionais` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `servicos`
--
ALTER TABLE `servicos` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profissional_servicos`
--
ALTER TABLE `profissional_servicos` ADD PRIMARY KEY (`id_profissional`, `id_servico`),
ADD KEY `id_servico` (`id_servico`);

--
-- Indexes for table `disponibilidade`
--
ALTER TABLE `disponibilidade` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `unique_slot` (`data`, `hora`, `profissionalId`),
ADD KEY `idx_id_servico_disp` (`id_servico`);

--
-- Indexes for table `agendamentos`
--
ALTER TABLE `agendamentos` ADD PRIMARY KEY (`id`),
ADD KEY `idx_id_servico_ag` (`id_servico`),
ADD KEY `idx_id_profissional_ag` (`id_profissional`),
ADD KEY `idx_id_disponibilidade_ag` (`id_disponibilidade`);

-- --------------------------------------------------------
--                  AUTO_INCREMENT
-- --------------------------------------------------------
--
-- AUTO_INCREMENT for table `profissionais`
--
ALTER TABLE `profissionais` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `servicos`
--
ALTER TABLE `servicos` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disponibilidade`
--
ALTER TABLE `disponibilidade` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agendamentos`
--
ALTER TABLE `agendamentos` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
--                  FOREIGN KEY CONSTRAINTS
-- --------------------------------------------------------
--
-- Constraints for table `profissional_servicos`
--
ALTER TABLE `profissional_servicos` ADD CONSTRAINT `fk_ps_profissional` FOREIGN KEY (`id_profissional`) REFERENCES `profissionais` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_ps_servico` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `disponibilidade`
--
ALTER TABLE `disponibilidade` ADD CONSTRAINT `fk_disp_servico` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id`);

--
-- Constraints for table `agendamentos`
--
ALTER TABLE `agendamentos` ADD CONSTRAINT `fk_ag_disponibilidade` FOREIGN KEY (`id_disponibilidade`) REFERENCES `disponibilidade` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_ag_profissional` FOREIGN KEY (`id_profissional`) REFERENCES `profissionais` (`id`),
ADD CONSTRAINT `fk_ag_servico` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id`);

COMMIT;