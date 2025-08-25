<?php
// Habilita a exibição de erros para depuração (remover em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carrega o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Define a URL base da aplicação dinamicamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptName = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', rtrim("$protocol://$host$scriptName", '/') . '/');

// Carrega as variáveis de ambiente do arquivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Inicia o roteador
$router = new App\Core\Router();

// Carrega as definições de rota do arquivo separado
require_once __DIR__ . '/../src/Routes/routes.php';

// Despacha a rota correspondente à URL atual
$router->dispatch();
