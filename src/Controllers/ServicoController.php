<?php

namespace App\Controllers;

use App\Models\ServicoModel;

class ServicoController
{
    private $model;

    public function __construct()
    {
        $this->model = new ServicoModel();
    }

    /**
     * Endpoint da API para adicionar um novo serviço.
     */
    public function apiAdd()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"));

        $nome = htmlspecialchars(strip_tags($data->nome ?? ''), ENT_QUOTES, 'UTF-8');
        $duracao = filter_var($data->duracao ?? 0, FILTER_VALIDATE_INT);
        $preco = filter_var($data->preco ?? 0, FILTER_VALIDATE_FLOAT);

        if (empty($nome) || $duracao <= 0) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Nome e duração (maior que zero) são obrigatórios.']);
            return;
        }

        $result = $this->model->create($nome, $duracao, $preco);

        if ($result['success']) {
            echo json_encode($result);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode($result);
        }
    }
}