<?php

namespace App\Controllers;

class PageController
{

    /**
     * Exibe a página Home.
     */
    public function home()
    {
        $pageTitle = 'Página Inicial';

        $faviconImg = 'assets/img/favicon.png';

        // Carrega o layout e a view da home
        require_once __DIR__ . '/../../views/partials/header.php';
        require_once __DIR__ . '/../../views/pages/home.php';
        require_once __DIR__ . '/../../views/partials/footer.php';
    }

    /**
     * Exibe a página Sobre.
     */
    public function about()
    {
        $pageTitle = 'Sobre Nós';

        // Carrega o layout e a view sobre
        require_once __DIR__ . '/../../views/partials/header.php';
        require_once __DIR__ . '/../../views/pages/sobre.php';
        require_once __DIR__ . '/../../views/partials/footer.php';
    }

    /**
     * Exibe o formulário de Contato.
     */
    public function contact()
    {
        $pageTitle = 'Contato';

        // Adiciona os scripts necessários para a página de contato
        $pageScripts = [
            'https://cdn.jsdelivr.net/npm/sweetalert2@11',
            'js/contact.js' // O script atualizado
        ];

        // Carrega o layout e a view de contato
        require_once __DIR__ . '/../../views/partials/header.php';
        require_once __DIR__ . '/../../views/pages/contact.php';
        require_once __DIR__ . '/../../views/partials/footer.php';
    }

}
