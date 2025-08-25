<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php'; // cria $pdo (PDO) e funções comuns

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

function route($method, $pattern, $handler) {
  static $routes = [];
  if ($handler) { $routes[] = compact('method','pattern','handler'); return; }
  return $routes;
}
function dispatch($method, $uri) {
  foreach (route(null, null, null) as $r) {
    $regex = '@^' . preg_replace('@\{([\w]+)\}@', '(?P<$1>[^/]+)', $r['pattern']) . '$@';
    if ($method === $r['method'] && preg_match($regex, $uri, $m)) {
      return $r['handler']($m);
    }
  }
  http_response_code(404); echo '404';
}

function findGroupBySlug(PDO $pdo, string $slug) {
  $stmt = $pdo->prepare('SELECT * FROM grupos WHERE slug = ? LIMIT 1');
  $stmt->execute([$slug]);
  return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/** MIDDLEWARE de contexto do grupo */
function withGroup(callable $handler) {
  return function($params) use ($handler) {
    global $pdo;
    $group = findGroupBySlug($pdo, $params['slug']);
    if (!$group) { http_response_code(404); echo 'Grupo não encontrado'; return; }
    return $handler($params, $group);
  };
}

/** Guard do painel */
function requireGroupMember(array $group) {
  if (!isset($_SESSION)) session_start();
  if (empty($_SESSION['user_id'])) {
    header('Location: /login?next=' . urlencode($_SERVER['REQUEST_URI'])); exit;
  }
  global $pdo;
  $stmt = $pdo->prepare('SELECT 1 FROM usuario_grupo WHERE usuario_id=? AND grupo_id=?');
  $stmt->execute([$_SESSION['user_id'], $group['id']]);
  if (!$stmt->fetch()) { http_response_code(403); echo 'Acesso negado ao grupo'; exit; }
}

/** ROTAS */

route('GET', '/g/{slug}', withGroup(function($params, $group) {
  global $pdo;
  $pro = $pdo->prepare('SELECT id,nome,especialidade FROM profissionais WHERE grupo_id=? ORDER BY nome');
  $pro->execute([$group['id']]);
  $profissionais = $pro->fetchAll(PDO::FETCH_ASSOC);
  include __DIR__ . '/../views/grupo/landing.php';
}));

route('GET', '/g/{slug}/profissionais', withGroup(function($params, $group) {
  global $pdo;
  $pro = $pdo->prepare('SELECT * FROM profissionais WHERE grupo_id=? ORDER BY nome');
  $pro->execute([$group['id']]);
  $profissionais = $pro->fetchAll(PDO::FETCH_ASSOC);
  include __DIR__ . '/../views/grupo/profissionais.php';
}));

route('GET', '/g/{slug}/agendar', withGroup(function($params, $group) {
  include __DIR__ . '/../views/grupo/agendar.php';
}));

route('POST', '/g/{slug}/agendar', withGroup(function($params, $group) {
  global $pdo;
  // validar que o profissional escolhido pertence ao grupo
  $profId = (int)($_POST['profissional_id'] ?? 0);
  $ck = $pdo->prepare('SELECT 1 FROM profissionais WHERE id=? AND grupo_id=?');
  $ck->execute([$profId, $group['id']]);
  if (!$ck->fetch()) { http_response_code(400); echo 'Profissional inválido para este grupo'; return; }

  // criar agendamento normalmente...
  // INSERT INTO agendamentos (profissional_id, cliente_id, data_hora, ...) VALUES ...
  echo 'Agendamento criado para o grupo ' . htmlspecialchars($group['nome']);
}));

route('GET', '/g/{slug}/painel', withGroup(function($params, $group) {
  requireGroupMember($group);
  include __DIR__ . '/../views/grupo/painel.php';
}));

dispatch($method, rtrim($uri, '/'));
