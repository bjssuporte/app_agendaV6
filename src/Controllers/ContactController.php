<?php

namespace App\Controllers;

use App\Mail\Mailer; // Importa a classe Mailer

/**
 * Lida com a lógica do formulário de contato.
 */
class ContactController
{
    /**
     * Processa o envio do formulário de contato via API.
     * Responde com JSON.
     */
    public function apiSend()
    {
        // // Define o cabeçalho da resposta como JSON
        header('Content-Type: application/json');

        // 1. Pega e sanitiza os dados do formulário
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

        // 2. Validação dos dados
        if (!$name || !$email || !$message) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success' => false,
                'message' => 'Por favor, preencha todos os campos corretamente.'
            ]);
            return;
        }

        // 3. Prepara o corpo do e-mail que será enviado
        $subject = "Nova mensagem de contato de {$name}";
        $body = "
            <html>
            <head>
                <title>{$subject}</title>
            </head>
            <body>
                <p><strong>Nome:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Mensagem:</strong></p>
                <p>{$message}</p>
            </body>
            </html>
        ";

        try {
            // 4. Tenta enviar o e-mail
            $mailer = new Mailer();

            // O e-mail será enviado para o endereço configurado no seu .env (MAIL_FROM_ADDRESS)
            $toAddress = $email;
            $toName = $name;

            if ($mailer->send($toAddress, $toName, $subject, $body)) {
                // 5. Responde com sucesso
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => "Obrigado pelo seu contato, {$name}! Sua mensagem foi enviada."
                ]);
            } else {
                // Lança uma exceção se o mailer->send retornar false
                throw new \Exception("Ocorreu uma falha no serviço de e-mail.");
            }
        } catch (\Exception $e) {
            // 6. Em caso de erro (validação, PHPMailer, etc.), responde com erro
            // Em um ambiente de produção, você poderia logar o erro: error_log($e->getMessage());
            http_response_code(500); // Internal Server Error
            echo json_encode([
                'success' => false,
                'message' => 'Desculpe, ocorreu um erro no servidor ao tentar enviar sua mensagem. Tente novamente mais tarde.'
            ]);
        }


        // $name = 'teste';

        // echo json_encode([
        //     'success' => true,
        //     'message' => "Obrigado pelo seu contato, {$name}! Sua mensagem foi enviada."
        // ]);
    }
}
