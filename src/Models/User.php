<?php

namespace App\Models;

use App\Config\Database;
use App\Database\DataSource;

class User
{
    protected DataSource $db;

    public function __construct()
    {
        // Obtém a instância única do DataSource. Não há mais lógica de conexão aqui.
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        $query = "SELECT id, name, email FROM users ORDER BY name ASC";
        return $this->db->selectAll($query);
    }

    public function findById($id)
    {
        $query = "SELECT id, name, email FROM users WHERE id = :id";
        return $this->db->select($query, [':id' => $id]);
    }

    public function create($data): string
    {
        $query = "INSERT INTO users (name, email) VALUES (:name, :email)";
        $params = [
            ':name' => $data['name'],
            ':email' => $data['email']
        ];
        return $this->db->insertWithLastId($query, $params);
    }

    public function update($id, $data): bool
    {
        $query = "UPDATE users SET name = :name, email = :email WHERE id = :id";
        $params = [
            ':id' => $id,
            ':name' => $data['name'],
            ':email' => $data['email']
        ];
        // O método update retorna o número de linhas afetadas.
        return $this->db->update($query, $params) > 0;
    }

    public function delete($id): bool
    {
        $query = "DELETE FROM users WHERE id = :id";
        $params = [':id' => $id];
        // O método delete retorna o número de linhas afetadas.
        return $this->db->delete($query, $params) > 0;
    }
}