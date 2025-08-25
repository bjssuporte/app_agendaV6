<?php

namespace App\Controllers;

use App\Models\AgendamentoModel;
use App\Models\ProfissionalModel;
use App\Models\GrupoModel; // NOVO: Importar o GrupoModel

class AgendamentoController
{
    private $model;
    private $profissionalModel;
    private $grupoModel; // NOVO: Adicionar propriedade para o GrupoModel

    public function __construct()
    {
        $this->model = new AgendamentoModel();
        $this->profissionalModel = new ProfissionalModel();
        $this->grupoModel = new GrupoModel(); // NOVO: Instanciar o GrupoModel
    }

    /**
     * Renderiza a página de agendamento GLOBAL
     */
    public function index()
    {
        $profissionais = $this->profissionalModel->getAll();

        $faviconImg = 'assets/img/favicon.png';
        $pageScriptsHeader = ['https://cdn.jsdelivr.net/npm/sweetalert2@11'];
        $pageScripts = ['js/agendamento.js'];
        $pageStyles = ['css/agendamento.css'];

        require_once __DIR__ . '/../../views/partials/header.php';
        require_once __DIR__ . '/../../views/pages/agendamento.php';
        require_once __DIR__ . '/../../views/partials/footer.php';
    }

    /**
     * Renderiza a página de administração GLOBAL
     */
    public function admin()
    {
        $profissionais = $this->profissionalModel->getAll();
        $servicoModel = new \App\Models\ServicoModel();
        $servicos = $servicoModel->getAll();

        $faviconImg = 'assets/img/favicon.png';
        $pageScriptsHeader = ['https://cdn.jsdelivr.net/npm/sweetalert2@11'];
        $pageScripts = ['js/admin_ag.js'];
        require_once __DIR__ . '/../../views/partials/header.php';
        require_once __DIR__ . '/../../views/pages/admin_ag.php';
        require_once __DIR__ . '/../../views/partials/footer.php';
    }

    // --- MÉTODOS DE API PARA PÁGINA GLOBAL ---

    public function getDatas($profissionalId = null)
    {
        header('Content-Type: application/json');
        $id = is_numeric($profissionalId) ? (int)$profissionalId : null;
        $idsParaFiltrar = $id ? [$id] : [];
        $datas = $this->model->getDatasDisponiveis($idsParaFiltrar);
        echo json_encode($datas);
    }

    public function getHorarios($data, $profissionalId = null)
    {
        header('Content-Type: application/json');
        $id = is_numeric($profissionalId) ? (int)$profissionalId : null;
        if ($id) {
            $horarios = $this->model->getHorariosPorData($data, [$id]);
        } else {
            $horarios = $this->model->getHorariosAgrupadosPorProfissional($data);
        }
        echo json_encode($horarios);
    }

    public function agendar()
    {
        header('Content-Type: application/json');
        $slotId = filter_input(INPUT_POST, 'slotId', FILTER_VALIDATE_INT);
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($slotId) || empty($nome) || empty($telefone) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Dados inválidos ou incompletos.']);
            return;
        }

        $result = $this->model->agendarHorario($slotId, $nome, $email, $telefone);

        if ($result['success']) {
            $slot = $result['slot'];
            $dateObj = \DateTime::createFromFormat('Y-m-d', $slot['data']);
            $formattedDate = $dateObj->format('d/m/Y');
            $timeObj = \DateTime::createFromFormat('H:i:s', $slot['hora']);
            $formattedTime = $timeObj->format('H:i');
            $summary = [
                'cliente' => $nome, 'email' => $email, 'telefone' => $telefone,
                'profissional' => $slot['profissionalNome'], 'data' => $formattedDate, 'hora' => $formattedTime
            ];
            $response = ['success' => true, 'summary' => $summary];
            echo json_encode($response);
        } else {
            http_response_code(409);
            echo json_encode($result);
        }
    }

    // --- MÉTODOS DE API PARA ADMIN GLOBAL ---
    
    public function getHorariosAdmin($data) { /* ...código existente... */ }
    public function addHorario() { /* ...código existente... */ }
    public function getHorarioById($id) { /* ...código existente... */ }
    public function updateHorario($id) { /* ...código existente... */ }
    public function deleteHorario($id) { /* ...código existente... */ }


    // --- NOVOS MÉTODOS DE API PARA PÁGINAS DE GRUPO ---

    /**
     * NOVO: Busca datas disponíveis para um grupo específico, filtrando por seu slug.
     */
    public function getDatasPorGrupo($slug, $profissionalId = null)
    {
        header('Content-Type: application/json');
        $grupo = $this->grupoModel->getBySlug($slug);
        if (!$grupo) {
            echo json_encode([]); return;
        }

        $idProfissionalFiltrado = is_numeric($profissionalId) ? (int)$profissionalId : null;

        if ($idProfissionalFiltrado) {
            // Se um profissional específico foi selecionado na página do grupo
            $idsParaFiltrar = [$idProfissionalFiltrado];
        } else {
            // Se "Todos" foi selecionado, busca por todos os profissionais do grupo
            $profissionaisDoGrupo = $this->profissionalModel->getByGroupId($grupo['id']);
            $idsParaFiltrar = array_column($profissionaisDoGrupo, 'id');
        }

        if (empty($idsParaFiltrar)) {
            echo json_encode([]); return;
        }
        
        $datas = $this->model->getDatasDisponiveis($idsParaFiltrar);
        echo json_encode($datas);
    }

    /**
     * NOVO: Busca horários para uma data em um grupo específico.
     */
    public function getHorariosPorGrupo($slug, $data, $profissionalId = null)
    {
        header('Content-Type: application/json');
        $grupo = $this->grupoModel->getBySlug($slug);
        if (!$grupo) {
            echo json_encode([]); return;
        }

        $idProfissionalFiltrado = is_numeric($profissionalId) ? (int)$profissionalId : null;

        if ($idProfissionalFiltrado) {
            // Retorna um array simples de horários para o profissional específico
            $horarios = $this->model->getHorariosPorData($data, [$idProfissionalFiltrado]);
        } else {
            // Retorna um objeto agrupado de horários para TODOS os profissionais do grupo
            $profissionaisDoGrupo = $this->profissionalModel->getByGroupId($grupo['id']);
            $idsDosProfissionaisDoGrupo = array_column($profissionaisDoGrupo, 'id');
            
            if(empty($idsDosProfissionaisDoGrupo)){
                 echo json_encode([]); return;
            }

            // Usa o método do model modificado para filtrar pelos IDs do grupo
            $horarios = $this->model->getHorariosAgrupadosPorProfissional($data, $idsDosProfissionaisDoGrupo);
        }

        echo json_encode($horarios);
    }
}