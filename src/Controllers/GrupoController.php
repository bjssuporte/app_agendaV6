<?php

namespace App\Controllers;

use App\Models\GrupoModel;
use App\Models\ProfissionalModel;

class GrupoController
{
    private $grupoModel;
    private $profissionalModel;

    public function __construct()
    {
        $this->grupoModel = new GrupoModel();
        $this->profissionalModel = new ProfissionalModel();
    }

    public function landingPage($slug)
    {
        $grupo = $this->grupoModel->getBySlug($slug);
        if (!$grupo) {
            // Redirecionar para 404
            http_response_code(404);
            require __DIR__ . '/../../views/pages/404.php';
            return;
        }

        $profissionais = $this->profissionalModel->getByGroupId($grupo['id']);

        // Carrega a view da landing page, passando os dados do grupo e seus profissionais
        $pageScripts = ['js/admin_ag.js'];
        require_once __DIR__ . '/../../views/pages/grupo_landing.php';
    }

    public function painelAdmin($slug)
    {
        // AQUI: Adicionar lógica de autenticação para garantir que o usuário logado
        // pertence a este grupo antes de mostrar o painel.

        $grupo = $this->grupoModel->getBySlug($slug);
        if (!$grupo) {
            http_response_code(404);
            require __DIR__ . '/../../views/pages/404.php';
            return;
        }

        $profissionais = $this->profissionalModel->getByGroupId($grupo['id']);
        // Reutiliza a view de admin, mas agora com dados filtrados
        require_once __DIR__ . '/../../views/pages/admin_ag_grupo.php';
    }
}
