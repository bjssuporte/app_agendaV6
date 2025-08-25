<?php
namespace App\Controllers;

class ReportController {
    
    public function index() {
        // Define o título e os assets específicos para esta página
        $pageTitle = 'Página de Relatórios';
        $pageStyles = ['/assets/css/relatorios.css']; // CSS específico
        $pageScripts = ['/assets/js/relatorios.js'];   // JS específico

        // Carrega o layout e a view
        require __DIR__ . '/../views/_partials/header.php';
        require __DIR__ . '/../views/reports.php';
        require __DIR__ . '/../views/_partials/footer.php';
    }
}