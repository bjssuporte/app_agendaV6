<?php
namespace App\Models;

use App\Config\Database;

class GrupoModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getBySlug(string $slug)
    {
        $query = "SELECT * FROM grupos WHERE slug = ?";
        return $this->db->select($query, [$slug]);
    }
}