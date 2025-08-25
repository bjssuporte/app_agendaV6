<?php

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true); // Habilita exceções

        // Configuração do servidor
        // $this->mail->SMTPDebug = SMTP::DEBUG_SERVER; // Descomente para debug

        $this->mail->CharSet = $_ENV['MAIL_CHARSET'];
        $this->mail->IsSMTP();
        // $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mail->Debugoutput = 'html';
        $this->mail->SMTPAuth   = true;
        $this->mail->SMTPSecure =  $_ENV['MAIL_ENCRYPTION'];
        $this->mail->Host       = $_ENV['MAIL_HOST'];
        $this->mail->Port       = $_ENV['MAIL_PORT'];
        $this->mail->Username   = $_ENV['MAIL_USERNAME'];
        $this->mail->Password   = $_ENV['MAIL_PASSWORD'];

        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Remetente
        $this->mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
    }

    public function send(string $toAddress, string $toName, string $subject, string $body): bool
    {
        try {
            // Destinatário
            // $this->mail->setFrom($_ENV['MAIL_USERNAME'], $toName);
            $this->mail->addAddress($toAddress, $toName);

            // Conteúdo
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            // $this->mail->AltBody = strip_tags($body); // Corpo alternativo para clientes que não leem HTML

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            // Em produção, logue o erro.
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        } finally {
            // Limpa os destinatários para o próximo envio, se o objeto for reutilizado.
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->clearBCCs();
        }
    }
}

// $mail = new App\Mail\mail();
// $mailer->send('destinatario@exemplo.com', 'Nome do Destinatário', 'Assunto do Email', '<h1>Corpo do E-mail</h1>');