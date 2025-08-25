<?php

namespace App\Controllers;

use App\Models\ProfissionalModel;

class ProfissionalController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProfissionalModel();
    }

    /**
     * Endpoint da API para adicionar um novo profissional.
     */
    public function apiAdd()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"));

        // Sanitização e Validação
        $nome = htmlspecialchars(strip_tags($data->nome ?? ''), ENT_QUOTES, 'UTF-8');
        $email = filter_var($data->email ?? null, FILTER_SANITIZE_EMAIL);
        $telefone = htmlspecialchars(strip_tags($data->telefone ?? ''), ENT_QUOTES, 'UTF-8'); // Opcional

        if (empty($nome) || empty($email)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Nome e e-mail são obrigatórios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Formato de e-mail inválido.']);
            return;
        }

        // Tenta criar o profissional no banco
        $result = $this->model->create($nome, $email, $telefone);

        if ($result['success']) {
            // Retorna sucesso com os dados do novo profissional para o JS
            echo json_encode($result);
        } else {
            // Retorna erro (ex: e-mail duplicado)
            http_response_code(409); // Conflict
            echo json_encode($result);
        }
    }

    // NOVO ENDPOINT: API para vincular um serviço.
    public function apiLinkService()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"));

        $id_profissional = filter_var($data->id_profissional ?? null, FILTER_VALIDATE_INT);
        $id_servico = filter_var($data->id_servico ?? null, FILTER_VALIDATE_INT);

        if (!$id_profissional || !$id_servico) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'IDs do profissional e do serviço são obrigatórios.']);
            return;
        }

        $result = $this->model->linkService($id_profissional, $id_servico);
        echo json_encode($result);
    }

    // NOVO ENDPOINT: API para buscar os serviços de um profissional.
    public function apiGetServices($id_profissional)
    {
        header('Content-Type: application/json');
        $services = $this->model->getServicesByProfessionalId($id_profissional);
        echo json_encode($services);
    }
}
