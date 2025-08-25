<?php

namespace App\Database;

use PDO;

/**
 * DataSource estende a classe Connection para fornecer métodos de alto nível 
 * para manipulação de dados (CRUD).
 */
class DataSource extends Connection
{
    /**
     * Construtor que passa as credenciais para a classe pai (Connection).
     */
    public function __construct(string $host, string $dbName, string $user, string $password)
    {
        parent::__construct($host, $dbName, $user, $password);
    }

    /**
     * Seleciona um único registro.
     * @return mixed Retorna um array associativo ou false se não encontrar.
     */
    public function select(string $query, array $params = [])
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Seleciona múltiplos registros.
     * @return array Retorna um array de arrays associativos.
     */
    public function selectAll(string $query, array $params = [])
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Executa uma instrução de inserção.
     * @return int O número de linhas afetadas.
     */
    public function insert(string $query, array $params): int
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    /**
     * Executa uma instrução de inserção e retorna o último ID inserido.
     * @return string O ID do último registro inserido.
     */
    public function insertWithLastId(string $query, array $params): string
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $this->conn->lastInsertId();
    }

    /**
     * Executa uma instrução de atualização.
     * @return int O número de linhas afetadas.
     */
    public function update(string $query, array $params = []): int
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Executa uma instrução de exclusão.
     * @return int O número de linhas afetadas.
     */
    public function delete(string $query, array $params): int
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}