document.addEventListener('DOMContentLoaded', function () {
    // Seleciona o formulário de contato.
    const contactForm = document.querySelector('#contactForm');
    const urlApi = 'api/contact/send';

    if (contactForm) {
        contactForm.addEventListener('submit', async function (event) {
            // 1. Previne o comportamento padrão do formulário.
            event.preventDefault();

            const submitButton = contactForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;

            // Desabilita o botão e mostra um feedback visual.
            submitButton.disabled = true;
            submitButton.textContent = 'Enviando...';

            const formData = new FormData(contactForm);

            // --- NOVO: Implementação de Timeout ---
            // O AbortController é a forma moderna de cancelar requisições fetch.
            const controller = new AbortController();
            const signal = controller.signal;
            const fetchTimeout = 25000; // Tempo em milissegundos (25 segundos)

            const timeoutId = setTimeout(() => {
                controller.abort(); // Cancela a requisição fetch
            }, fetchTimeout);
            // --- FIM DA IMPLEMENTAÇÃO DE TIMEOUT ---

            try {
                // 2. Envia os dados para a API, agora com o controle de timeout.
                const sendFormData = new FormData(contactForm);

                const response = await fetch(urlApi, {
                    method: 'POST',
                    body: sendFormData
                });

                const result = await response.json();
                console.log(result);

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Enviado!',
                        text: result.message,
                        confirmButtonColor: '#4f46e5',
                        timer: 3500,
                        willClose: () => {
                            contactForm.reset();
                            window.location.href = "home";
                        },
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: result.message || 'Ocorreu um erro desconhecido.',
                        confirmButtonColor: '#4f46e5'
                    });
                }

            } catch (error) {
                console.error('Erro ao redefinir senha via fetch:', error);
                showMessage('Ocorreu um erro ao tentar redefinir sua senha. Tente novamente.', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
                contactForm.reset();
            }
        });
    }
});
