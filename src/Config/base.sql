CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Opcional: Inserir alguns dados de exemplo
INSERT INTO users (name, email) VALUES
('Ada Lovelace', 'ada.lovelace@example.com'),
('Grace Hopper', 'grace.hopper@example.com'),
('Margaret Hamilton', 'margaret.hamilton@example.com');



-- Tabela para os profissionais/prestadores de serviço
CREATE TABLE profissionais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    telefone VARCHAR(20)
);

-- Tabela para os serviços oferecidos
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    duracao_min INT NOT NULL, -- Duração em minutos
    preco DECIMAL(10, 2)
);

-- Tabela para os agendamentos
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_servico INT,
    id_profissional INT,
    nome_cliente VARCHAR(255) NOT NULL,
    email_cliente VARCHAR(255) NOT NULL,
    data_hora DATETIME NOT NULL,
    status ENUM('confirmado', 'cancelado', 'concluido') DEFAULT 'confirmado',
    FOREIGN KEY (id_servico) REFERENCES servicos(id),
    FOREIGN KEY (id_profissional) REFERENCES profissionais(id)
);

CREATE TABLE IF NOT EXISTS disponibilidade (
  id INT AUTO_INCREMENT PRIMARY KEY,
  profissionalId VARCHAR(50) NOT NULL DEFAULT 'daniela01',
  profissionalNome VARCHAR(255) NOT NULL DEFAULT 'Daniela (BH - Santa Efigênia)',
  data DATE NOT NULL,
  hora TIME NOT NULL,
  status ENUM('disponivel', 'agendado') DEFAULT 'disponivel',
  clienteNome VARCHAR(255),
  clienteEmail VARCHAR(255),
  -- Garante que um profissional não pode ter o mesmo horário duas vezes no mesmo dia
  UNIQUE KEY `unique_slot` (`data`, `hora`, `profissionalId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;