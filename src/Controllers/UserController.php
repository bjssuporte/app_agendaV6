<?php

namespace App\Controllers;

use App\Models\User;

class UserController {

    public function index() {
        $pageTitle = 'Gerenciamento de Usuários';
        $pageScripts = [
            'https://cdn.jsdelivr.net/npm/sweetalert2@11',
            'js/users.js' 
        ];

        require __DIR__ . '/../../views/partials/header.php';
        require_once __DIR__ . '/../../views/pages/users.php';
        require __DIR__ . '/../../views/partials/footer.php';
    }

    public function apiGetAll() {
        header('Content-Type: application/json');
        try {
            $userModel = new User();
            $users = $userModel->getAll();
            echo json_encode($users);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Erro no servidor: ' . $e->getMessage()]);
        }
    }

    // ==================================================================
    // ALTERAÇÃO PRINCIPAL AQUI: O método apiSave foi dividido em dois.
    // ==================================================================

    /**
     * NOVO: Método específico para CRIAR usuários.
     * É chamado pela rota POST /api/users/create
     */
    public function apiCreate() {
        header('Content-Type: application/json');
        
        $data = $_POST;

        if (empty($data['name']) || empty($data['email'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Dados inválidos. Nome e email são obrigatórios.']);
            return;
        }

        try {
            $userModel = new User();
            $newUserId = $userModel->create($data);
            $savedUser = $userModel->findById($newUserId);
            
            http_response_code(201); // 201 Created
            echo json_encode(['message' => 'Usuário criado com sucesso!', 'user' => $savedUser]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Erro ao criar usuário: ' . $e->getMessage()]);
        }
    }

    /**
     * NOVO: Método específico para ATUALIZAR usuários.
     * É chamado pela rota POST /api/users/update/{id}
     */
    public function apiUpdate($id) {
        header('Content-Type: application/json');
        
        // Dados vêm do corpo da requisição POST
        $data = $_POST;

        if (empty($id) || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['message' => 'ID do usuário inválido fornecido na URL.']);
            return;
        }

        if (empty($data['name']) || empty($data['email'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Dados inválidos. Nome e email são obrigatórios.']);
            return;
        }

        try {
            $userModel = new User();
            // Verifica se o usuário existe antes de atualizar (opcional, mas boa prática)
            $existingUser = $userModel->findById($id);
            if (!$existingUser) {
                http_response_code(404); // Not Found
                echo json_encode(['message' => 'Usuário não encontrado para atualização.']);
                return;
            }
            
            $userModel->update($id, $data);
            $updatedUser = $userModel->findById($id);

            http_response_code(200); // 200 OK
            echo json_encode(['message' => 'Usuário atualizado com sucesso!', 'user' => $updatedUser]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Erro ao atualizar usuário: ' . $e->getMessage()]);
        }
    }

    public function apiDelete($id) {
        header('Content-Type: application/json');

        if (empty($id) || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['message' => 'ID do usuário inválido.']);
            return;
        }

        try {
            $userModel = new User();
            if ($userModel->delete($id)) {
                 echo json_encode(['message' => 'Usuário deletado com sucesso.']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Usuário não encontrado.']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Erro ao deletar usuário: ' . $e->getMessage()]);
        }
    }
}
