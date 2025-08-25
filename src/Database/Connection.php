<?php

namespace App\Database;

use PDO;
use PDOException;

/**
 * Classe base para estabelecer a conexão com o banco de dados via PDO.
 */
class Connection
{
    /**
     * @var PDO|null A instância da conexão PDO.
     */
    protected ?PDO $conn;

    /**
     * O construtor recebe as credenciais e estabelece a conexão.
     */
    public function __construct(string $host, string $dbName, string $user, string $password)
    {
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
            $this->conn = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            // Em produção, grave o erro em um log e mostre uma mensagem genérica.
            error_log("Database Connection Error: " . $e->getMessage());
            die('Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.');
        }
    }

    /**
     * Retorna a instância da conexão PDO ativa.
     * @return PDO|null
     */
    public function getConnection(): ?PDO
    {
        return $this->conn;
    }

    /**
     * Fecha a conexão com o banco de dados.
     */
    public function closeConnection(): void
    {
        $this->conn = null;
    }
}