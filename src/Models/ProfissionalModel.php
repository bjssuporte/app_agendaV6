<?php

namespace App\Models;

use App\Config\Database;
use App\Database\DataSource;
use Exception;

class ProfissionalModel
{
    /** @var DataSource */
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Busca todos os profissionais cadastrados.
     * @return array
     */
    public function getAll(): array
    {
        $query = "SELECT id, nome FROM profissionais ORDER BY nome ASC";
        return $this->db->selectAll($query);
    }

    /**
     * Cria um novo profissional no banco de dados.
     * @return array Retorna o resultado da operação.
     */
    public function create(string $nome, string $email, ?string $telefone): array
    {
        $query = "INSERT INTO profissionais (nome, email, telefone) VALUES (?, ?, ?)";
        try {
            // Usamos insertWithLastId para poder retornar o ID do novo profissional
            $lastId = $this->db->insertWithLastId($query, [$nome, $email, $telefone]);
            return ['success' => true, 'id' => $lastId, 'nome' => $nome];
        } catch (Exception $e) {
            // Código '23000' é para violação de constraint (ex: e-mail duplicado)
            if ($e->getCode() == '23000') {
                return ['success' => false, 'message' => 'Este e-mail já está cadastrado.'];
            }
            error_log("CreateProfissional Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao cadastrar profissional.'];
        }
    }

    public function linkService(int $id_profissional, int $id_servico): array
    {
        $query = "INSERT INTO profissional_servicos (id_profissional, id_servico) VALUES (?, ?)";
        try {
            $this->db->insert($query, [$id_profissional, $id_servico]);
            return ['success' => true, 'message' => 'Serviço vinculado com sucesso!'];
        } catch (Exception $e) {
            if ($e->getCode() == '23000') {
                return ['success' => false, 'message' => 'Este serviço já está vinculado a este profissional.'];
            }
            error_log("LinkService Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao vincular serviço.'];
        }
    }

    // NOVO MÉTODO: Busca todos os serviços de um profissional específico.
    public function getServicesByProfessionalId(int $id_profissional): array
    {
        $query = "SELECT s.id, s.nome 
                FROM servicos s
                INNER JOIN profissional_servicos ps ON s.id = ps.id_servico
                WHERE ps.id_profissional = ?
                ORDER BY s.nome ASC";
        return $this->db->selectAll($query, [$id_profissional]);
    }


    // Adicione este novo método dentro da classe ProfissionalModel
    public function getByGroupId(int $id_grupo): array
    {
        $query = "SELECT id, nome FROM profissionais WHERE id_grupo = ? ORDER BY nome ASC";
        return $this->db->selectAll($query, [$id_grupo]);
    }
}
