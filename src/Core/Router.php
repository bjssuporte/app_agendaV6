<?php

namespace App\Core;

class Router
{

    protected $routes = [
        'GET' => [],
        'POST' => [],
        'DELETE' => []
    ];

    public function get($uri, $controllerAction)
    {
        $this->routes['GET'][$uri] = $controllerAction;
    }

    public function post($uri, $controllerAction)
    {
        $this->routes['POST'][$uri] = $controllerAction;
    }

    public function delete($uri, $controllerAction)
    {
        $this->routes['DELETE'][$uri] = $controllerAction;
    }

    public function dispatch()
    {
        // Usamos $_GET['url'] para compatibilidade com o .htaccess
        $uri = trim($_GET['url'] ?? '', '/');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes[$method] as $route => $action) {
            // ==================================================================
            // CORREÇÃO PRINCIPAL AQUI
            // ==================================================================
            // A expressão regular foi alterada de '(\w+)' para '([^\/]+)'.
            // O '(\w+)' anterior só capturava letras, números e underscore, mas não o hífen (-)
            // que existe nas datas (ex: 2025-08-13).
            // O novo '([^\/]+)' captura qualquer caractere exceto a barra (/),
            // o que resolve o problema para as rotas que passam datas como parâmetro.
            $regex = "#^" . preg_replace('/\{\w+\}/', '([^\/]+)', $route) . "$#";

            // Verifica se a URI atual corresponde à rota da vez
            if (preg_match($regex, $uri, $matches)) {
                // Remove o primeiro elemento ($matches[0]), que é a string da URI completa
                array_shift($matches);

                $controllerName = $action[0];
                $methodName = $action[1];

                $controllerInstance = new $controllerName();

                // Chama o método do controller, passando os parâmetros capturados da URL
                call_user_func_array([$controllerInstance, $methodName], $matches);
                return; // Encontrou a rota, encerra a execução
            }
        }

        // Se nenhuma rota corresponder, exibe a página 404.
        http_response_code(404);
        $path_404 = __DIR__ . '/../../views/pages/404.php';
        if (file_exists($path_404)) {
            require $path_404;
        } else {
            echo "<h1>404 - Página não encontrada</h1>";
        }
    }
}
