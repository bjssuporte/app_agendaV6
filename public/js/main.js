document.addEventListener('click', async (e) => {
    // Verifica se o clique foi em um link da classe 'ajax-link'
    const link = e.target.closest('.ajax-link');
    if (link) {
        e.preventDefault(); // Impede a navegação padrão
        const url = '/api/relatorios/content'; // A URL da nossa API de conteúdo

        try {
            const response = await fetch(url);
            const html = await response.text();

            // Injeta o novo conteúdo
            document.getElementById('content-area').innerHTML = html;

            // ATIVA O SCRIPT ESPECÍFICO DA PÁGINA
            // Como o script relatorios.js não foi carregado,
            // precisamos executar sua lógica aqui ou carregá-lo dinamicamente.
            initializeReportPage();

        } catch (error) {
            console.error('Falha ao carregar conteúdo:', error);
        }
    }
});

// Função que seria o conteúdo de relatorios.js
function initializeReportPage() {
    const button = document.getElementById('btn-alerta-relatorio');
    if (button) {
        button.addEventListener('click', () => {
            Swal.fire('Carregado via AJAX!', 'Este conteúdo foi inserido dinamicamente.', 'success');
        });
    }
}