<?php

namespace App\Config;

use App\Database\DataSource; // Importa a classe DataSource

class Database
{
    /**
     * @var DataSource|null A única instância da classe DataSource.
     */
    private static ?DataSource $instance = null;

    /**
     * O construtor é privado para impedir a criação de instâncias diretas.
     */
    private function __construct() {}

    /**
     * Impede a clonagem da instância.
     */
    private function __clone() {}

    /**
     * Impede a desserialização da instância.
     */
    public function __wakeup() {}

    /**
     * Método estático que controla o acesso à instância Singleton.
     *
     * @return DataSource A instância do DataSource.
     */
    public static function getInstance(): DataSource
    {
        // Se a instância ainda não foi criada, crie-a.
        if (self::$instance === null) {
            // As variáveis de ambiente são carregadas no index.php
            $dbHost = $_ENV['DB_HOST'];
            $dbName = $_ENV['DB_DATABASE'];
            $dbUser = $_ENV['DB_USERNAME'];
            $dbPass = $_ENV['DB_PASSWORD'];

            // Cria a nova e única instância do DataSource
            self::$instance = new DataSource($dbHost, $dbName, $dbUser, $dbPass);
        }

        return self::$instance;
    }
}