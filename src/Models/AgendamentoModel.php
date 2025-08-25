<?php

namespace App\Models;

use App\Config\Database;
use App\Database\DataSource;
use PDO;
use Exception;

class AgendamentoModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * MODIFICADO: Agora aceita um ID de profissional opcional para filtrar as datas.
     */
    public function getDatasDisponiveis(array $profissionalIds = []): array
    {
        $query = "SELECT DISTINCT data FROM disponibilidade WHERE status = 'disponivel' AND data >= CURDATE()";
        $params = [];

        if (!empty($profissionalIds)) {
            // Cria placeholders (?, ?, ?) para a cláusula IN
            $placeholders = implode(',', array_fill(0, count($profissionalIds), '?'));
            $query .= " AND profissionalId IN ($placeholders)";
            $params = $profissionalIds;
        }

        $query .= " ORDER BY data ASC";
        $result = $this->db->selectAll($query, $params);
        return array_column($result, 'data');
    }

    /**
     * Busca uma lista simples de horários para uma data, opcionalmente filtrada por um array de IDs de profissionais.
     * Ideal para a visão de agendamento quando um profissional específico é selecionado.
     *
     * @param string $data A data no formato 'YYYY-MM-DD'.
     * @param array $profissionalIds Um array de IDs de profissionais para filtrar os resultados.
     * @return array Retorna um array de horários.
     */
    public function getHorariosPorData(string $data, array $profissionalIds = []): array
    {
        // Query base para buscar horários disponíveis em uma data
        $query = "SELECT id, hora FROM disponibilidade WHERE data = ? AND status = 'disponivel'";
        $params = [$data];

        // Se um array de IDs de profissionais foi fornecido, adiciona o filtro
        if (!empty($profissionalIds)) {
            // Cria os placeholders (?) para a cláusula IN do SQL, um para cada ID
            $placeholders = implode(',', array_fill(0, count($profissionalIds), '?'));

            // Adiciona a cláusula IN na query
            $query .= " AND profissionalId IN ($placeholders)";

            // Combina os parâmetros da data com os IDs dos profissionais
            $params = array_merge($params, $profissionalIds);
        }

        $query .= " ORDER BY hora ASC";

        return $this->db->selectAll($query, $params);
    }
    /**
     * Retorna horários de uma data agrupados por nome do profissional.
     * Se um array de IDs de profissionais for fornecido, o resultado é filtrado para incluir apenas eles.
     *
     * @param string $data A data no formato 'YYYY-MM-DD'.
     * @param array $profissionalIds Um array opcional de IDs de profissionais para filtrar a busca.
     * @return array Retorna um array associativo onde as chaves são nomes de profissionais.
     */
    public function getHorariosAgrupadosPorProfissional(string $data, array $profissionalIds = []): array
    {
        // Query base para buscar os dados necessários
        $query = "SELECT id, hora, profissionalNome 
                  FROM disponibilidade 
                  WHERE data = ? AND status = 'disponivel'";

        $params = [$data];

        // Se um array de IDs foi passado (ex: para filtrar por um grupo), adiciona a cláusula IN
        if (!empty($profissionalIds)) {
            $placeholders = implode(',', array_fill(0, count($profissionalIds), '?'));
            $query .= " AND profissionalId IN ($placeholders)";
            $params = array_merge($params, $profissionalIds);
        }

        $query .= " ORDER BY profissionalNome ASC, hora ASC";

        // Executa a busca no banco de dados
        $horariosDoBanco = $this->db->selectAll($query, $params);

        // Agrupa os resultados em um array PHP
        $agrupados = [];
        foreach ($horariosDoBanco as $horario) {
            $agrupados[$horario['profissionalNome']][] = [
                'id' => $horario['id'],
                'hora' => $horario['hora']
            ];
        }

        return $agrupados;
    }


    public function getHorariosByDateAdmin(string $data): array
    {
        $query = "SELECT 
                d.id, d.profissionalId, d.profissionalNome, 
                d.id_servico, d.data, d.hora, d.status, 
                d.clienteNome, d.clienteEmail, d.clienteTelefone,
                s.nome as servicoNome 
              FROM disponibilidade d
              LEFT JOIN servicos s ON d.id_servico = s.id
              WHERE d.data = ? 
              ORDER BY d.hora ASC";
        return $this->db->selectAll($query, [$data]);
    }

    public function addHorario(string $data, string $hora, string $profissionalId, string $profissionalNome, ?int $id_servico): array
    {
        // ... (lógica para adicionar não muda) ...
        $query = "INSERT INTO disponibilidade (data, hora, profissionalId, profissionalNome, id_servico) VALUES (?, ?, ?, ?, ?)";
        try {
            $this->db->insert($query, [$data, $hora, $profissionalId, $profissionalNome, $id_servico]);
            return ['success' => true];
        } catch (Exception $e) {
            if ($e->getCode() == '23000') { // Código de erro para violação de chave única
                return ['success' => false, 'message' => 'Este horário já está cadastrado para este profissional.'];
            }
            return ['success' => false, 'message' => 'Ocorreu um erro ao adicionar o horário.'];
        }
    }

    /**
     * NOVO: Busca um único horário por ID.
     */
    public function getHorarioById(int $id)
    {
        $query = "SELECT * FROM disponibilidade WHERE id = ?";
        return $this->db->select($query, [$id]);
    }

    /**
     * NOVO: Atualiza um horário de disponibilidade e o agendamento correspondente, se houver.
     */
    public function updateHorario(int $id, array $data): array
    {
        $pdo = $this->db->getConnection();
        try {
            $pdo->beginTransaction();

            // Atualiza a tabela de disponibilidade
            $queryDisp = "UPDATE disponibilidade SET profissionalId = ?, profissionalNome = ?, id_servico = ?, data = ?, hora = ? WHERE id = ?";
            $stmtDisp = $pdo->prepare($queryDisp);
            $stmtDisp->execute([
                $data['profissionalId'],
                $data['profissionalNome'],
                $data['id_servico'],
                $data['data'],
                $data['hora'],
                $id
            ]);

            // Se o horário estiver agendado, atualiza também a tabela de agendamentos
            if ($data['status'] === 'agendado') {
                $newDataHora = $data['data'] . ' ' . $data['hora'];
                $queryAgend = "UPDATE agendamentos SET id_profissional = ?, id_servico = ?, data_hora = ? WHERE id_disponibilidade = ?";
                $stmtAgend = $pdo->prepare($queryAgend);
                $stmtAgend->execute([
                    $data['profissionalId'],
                    $data['id_servico'],
                    $newDataHora,
                    $id
                ]);
            }

            $pdo->commit();
            return ['success' => true, 'message' => 'Horário atualizado com sucesso!'];
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("UpdateHorario Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao atualizar o horário: ' . $e->getMessage()];
        }
    }

    /**
     * MODIFICADO: Exclui um horário. Se estiver agendado, exclui o agendamento correspondente primeiro.
     */
    public function deleteHorario(int $id): array
    {
        // A constraint ON DELETE CASCADE no banco de dados já cuida da exclusão em cascata.
        // Apenas deletar da tabela `disponibilidade` é suficiente.
        $query = "DELETE FROM disponibilidade WHERE id = ?";
        try {
            $affectedRows = $this->db->delete($query, [$id]);
            if ($affectedRows > 0) {
                return ['success' => true, 'message' => 'Horário removido com sucesso.'];
            } else {
                return ['success' => false, 'message' => 'Nenhum horário encontrado para remover.'];
            }
        } catch (Exception $e) {
            error_log("DeleteHorario Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro ao remover o horário.'];
        }
    }

    /**
     * MODIFICADO: Salva o ID da disponibilidade no agendamento.
     */
    public function agendarHorario(int $slotId, string $nome, string $email, string $telefone): array
    {
        $pdo = $this->db->getConnection();
        try {
            $pdo->beginTransaction();

            $querySelect = "SELECT * FROM disponibilidade WHERE id = ? AND status = 'disponivel' FOR UPDATE";
            $stmtSelect = $pdo->prepare($querySelect);
            $stmtSelect->execute([$slotId]);
            $slot = $stmtSelect->fetch(PDO::FETCH_ASSOC);

            if (!$slot) {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'Ops! Este horário não está mais disponível.'];
            }

            // Atualiza a disponibilidade
            $queryUpdate = "UPDATE disponibilidade SET status = 'agendado', clienteNome = ?, clienteEmail = ?, clienteTelefone = ? WHERE id = ?";
            $stmtUpdate = $pdo->prepare($queryUpdate);
            $stmtUpdate->execute([$nome, $email, $telefone, $slotId]);

            // Insere na tabela de agendamentos com o ID da disponibilidade
            $dataHora = $slot['data'] . ' ' . $slot['hora'];
            $queryInsert = "INSERT INTO agendamentos (id_disponibilidade, id_profissional, id_servico, nome_cliente, email_cliente, telefone_cliente, data_hora, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmado')";
            $stmtInsert = $pdo->prepare($queryInsert);
            $stmtInsert->execute([
                $slotId, // <-- VÍNCULO SENDO CRIADO AQUI
                $slot['profissionalId'],
                $slot['id_servico'],
                $nome,
                $email,
                $telefone,
                $dataHora
            ]);

            $pdo->commit();
            return ['success' => true, 'slot' => $slot];
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("AgendarHorario Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno ao agendar. Tente novamente.'];
        }
    }
}
