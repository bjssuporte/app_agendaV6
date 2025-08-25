<?php

namespace App\Models;

use App\Config\Database;

class ServicoModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Busca todos os serviços cadastrados.
     * @return array
     */
    public function getAll(): array
    {
        $query = "SELECT id, nome FROM servicos ORDER BY nome ASC";
        return $this->db->selectAll($query);
    }

    /**
     * Adiciona um novo serviço.
     */
    public function create(string $nome, int $duracao, ?float $preco): array
    {
        $query = "INSERT INTO servicos (nome, duracao_min, preco) VALUES (?, ?, ?)";
        try {
            $lastId = $this->db->insertWithLastId($query, [$nome, $duracao, $preco]);
            return ['success' => true, 'id' => $lastId, 'nome' => $nome];
        } catch (\Exception $e) {
            error_log("CreateServico Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao cadastrar serviço.'];
        }
    }
}