<?php

// --- ROTAS DE PÁGINAS (Renderizam HTML) ---
$router->get('', ['App\Controllers\PageController', 'home']);
$router->get('home', ['App\Controllers\PageController', 'home']);
$router->get('sobre', ['App\Controllers\PageController', 'about']);
$router->get('contato', ['App\Controllers\PageController', 'contact']);
$router->get('agendamento', ['App\Controllers\AgendamentoController', 'index']);
$router->get('admin_ag', ['App\Controllers\AgendamentoController', 'admin']);
$router->get('users', ['App\Controllers\UserController', 'index']);

// --- ROTAS DA API (Respondem com JSON para o JavaScript) ---

// API de Usuários
$router->get('api/users', ['App\Controllers\UserController', 'apiGetAll']);
$router->post('api/users/create', ['App\Controllers\UserController', 'apiCreate']);
$router->post('api/users/update/{id}', ['App\Controllers\UserController', 'apiUpdate']);
$router->delete('api/users/{id}', ['App\Controllers\UserController', 'apiDelete']);

// API de Contato
$router->post('api/contact/send', ['App\Controllers\ContactController', 'apiSend']);

// ===== ROTAS DE AGENDAMENTO MODIFICADAS =====
// Rotas para buscar datas disponíveis (com ou sem filtro de profissional)
$router->get('api/agendamento/datas', ['App\Controllers\AgendamentoController', 'getDatas']);
$router->get('api/agendamento/datas/profissional/{id}', ['App\Controllers\AgendamentoController', 'getDatas']);

// Rotas para buscar horários (com ou sem filtro de profissional)
$router->get('api/agendamento/horarios/{data}', ['App\Controllers\AgendamentoController', 'getHorarios']);
$router->get('api/agendamento/horarios/{data}/profissional/{id}', ['App\Controllers\AgendamentoController', 'getHorarios']);

// Rota para realizar o agendamento
$router->post('api/agendamento/agendar', ['App\Controllers\AgendamentoController', 'agendar']);

// Rotas da área administrativa
$router->get('api/agendamento/admin/horarios/{data}', ['App\Controllers\AgendamentoController', 'getHorariosAdmin']);
$router->post('api/agendamento/admin/add', ['App\Controllers\AgendamentoController', 'addHorario']);
$router->get('api/agendamento/admin/horario/{id}', ['App\Controllers\AgendamentoController', 'getHorarioById']); 
$router->post('api/agendamento/admin/update/{id}', ['App\Controllers\AgendamentoController', 'updateHorario']); 
$router->post('api/agendamento/admin/delete/{id}', ['App\Controllers\AgendamentoController', 'deleteHorario']);
// ============================================

// API de Profissionais e Serviços
$router->post('api/profissionais/add', ['App\Controllers\ProfissionalController', 'apiAdd']);
$router->get('api/profissionais/{id}/servicos', ['App\Controllers\ProfissionalController', 'apiGetServices']);
$router->post('api/servicos/add', ['App\Controllers\ServicoController', 'apiAdd']);
$router->post('api/profissionais/link-servico', ['App\Controllers\ProfissionalController', 'apiLinkService']);

// Rota para a landing page PÚBLICA do grupo
$router->get('agendamento/g/{slug}', ['App\Controllers\GrupoController', 'landingPage']);

// Rota para o painel de ADMIN do grupo
$router->get('admin/g/{slug}', ['App\Controllers\GrupoController', 'painelAdmin']);

// Novas rotas de API para buscar dados filtrados por grupo
$router->get('api/agendamento/g/{slug}/datas', ['App\Controllers\AgendamentoController', 'getDatasPorGrupo']);
$router->get('api/agendamento/g/{slug}/horarios/{data}', ['App\Controllers\AgendamentoController', 'getHorariosPorGrupo']);