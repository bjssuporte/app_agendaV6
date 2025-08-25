<?php

namespace App\Controllers;

use Config\Database;
use PDO;

class HomeController
{
    // Dentro de src/App/Controllers/HomeController.php
    public function index()
    {
        // Variáveis que podem ser passadas para o template
        $pageTitle = "Página Inicial Dinâmica";
        $welcomeMessage = "Bem-vindo ao esqueleto do projeto MVC!";

        // Carrega a view (template) e passa as variáveis para ela
        $this->view('home', [
            'title' => $pageTitle,
            'message' => $welcomeMessage
        ]);
    }

    public function sobre()
    {
        // Variáveis que podem ser passadas para o template
        $pageTitle = "Página Sobre";
        $welcomeMessage = "Bem-vindo ao esqueleto do projeto Sobre!";

        // Carrega a view (template) e passa as variáveis para ela
        $this->view('sobre', [
            'title' => $pageTitle,
            'message' => $welcomeMessage
        ]);
    }

    // public function testDatabase()
    // {
    //     try {
    //         $pdo = Database::getInstance();
    //         echo "<h1>✅ Conexão com o banco de dados bem-sucedida!</h1>";
    //     } catch (\PDOException $e) {
    //         echo "<h1>❌ Falha na conexão com o banco de dados:</h1>";
    //         echo "<p style='color:red; font-family: monospace; background-color: #ffecec; padding: 15px; border-radius: 5px;'>" . $e->getMessage() . "</p>";
    //         echo "<p>Verifique suas credenciais no arquivo <strong>.env</strong>.</p>";
    //     }
    // }

    // public function testEmail()
    // {
    //     echo "<h1>📧 Teste de Envio de E-mail</h1>";
    //     echo "<p>Esta página demonstra como a classe Mailer seria chamada. As credenciais são lidas do seu arquivo <strong>.env</strong>.</p>";
    //     echo "<pre style='background-color:#f0f0f0; padding: 15px; border-radius: 5px;'><code>";
    //     echo htmlspecialchars(
    //         '// Use este código em seus controladores:
    //             // require_once __DIR__ . "/../../vendor/autoload.php";
    //             // use App\Mail\Mailer;

    //             $mailer = new \App\Mail\Mailer();
    //             $success = $mailer->send(
    //                 \'destinatario@exemplo.com\', 
    //                 \'Nome do Destinatário\', 
    //                 \'Assunto de Teste\', 
    //                 \'<h1>Olá!</h1><p>Este é um e-mail de teste.</p>\'
    //             );

    //             if ($success) {
    //                 echo "Simulação: E-mail enviado com sucesso!";
    //             } else {
    //                 echo "Simulação: Falha ao enviar e-mail. Verifique os logs e as configs no .env.";
    //             }'
    //     );
    //     echo "</code></pre>";
    // }


    private function view(string $viewName, array $data = [])
    {

        extract($data); // <--- Ponto crucial!

        // ...
        // Monta o caminho: __DIR__ . "/../../../views/home.php"
        $viewFile = __DIR__ . "/../../../views/{$viewName}.php";

        if (($viewFile)) {
            require_once $viewFile; // <== AQUI o arquivo home.php é finalmente carregado!
        } else {
            // Lida com o erro de view não encontrada
            echo "Erro: View '$viewName' não encontrada.";
        }
    }
}
