document.addEventListener('DOMContentLoaded', () => {
    // --- ELEMENTOS DO DOM ---
    const userList = document.getElementById('user-list');
    const userForm = document.getElementById('user-form');
    const formTitle = document.getElementById('form-title');
    const saveButton = document.getElementById('save-button');
    const userIdInput = document.getElementById('user-id');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const loadingSpinner = document.getElementById('loading-spinner');
    const errorMessageDiv = document.getElementById('error-message');
    const addUserButtonContainer = document.getElementById('add-user-button-container');
    const formSection = document.getElementById('form-section');

    // ==================================================================
    // ALTERAÇÃO PRINCIPAL AQUI: Funções da API separadas
    // ==================================================================
    const API_BASE_URL = 'api/users';

    const api = {
        getUsers: async () => {
            const response = await fetch(API_BASE_URL);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        },
        // NOVO: Função específica para criar usuário
        createUser: async (formData) => {
            const response = await fetch(`${API_BASE_URL}/create`, {
                method: 'POST',
                body: formData
            });
            if (!response.ok) {
                 const errorData = await response.json();
                 throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }
            return response.json();
        },
        // NOVO: Função específica para atualizar usuário
        updateUser: async (id, formData) => {
            const response = await fetch(`${API_BASE_URL}/update/${id}`, {
                method: 'POST', // Usando POST por simplicidade com FormData
                body: formData
            });
            if (!response.ok) {
                 const errorData = await response.json();
                 throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }
            return response.json();
        },
        deleteUser: async (id) => {
            const response = await fetch(`${API_BASE_URL}/${id}`, {
                method: 'DELETE'
            });
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }
            return response.json();
        }
    };

    // --- Lógica de renderização e UI (sem grandes alterações) ---
    const showLoading = (isLoading) => {
        loadingSpinner.style.display = isLoading ? 'block' : 'none';
    };
    const showError = (message) => {
        errorMessageDiv.textContent = message;
        errorMessageDiv.classList.remove('hidden');
    };
    const hideError = () => {
        errorMessageDiv.classList.add('hidden');
    };

    const renderUsers = async () => {
        showLoading(true);
        hideError();
        userList.innerHTML = '';
        try {
            const users = await api.getUsers();
            if (users.length === 0) {
                userList.innerHTML = `<p class="text-center text-gray-500 py-4">Nenhum usuário cadastrado.</p>`;
            } else {
                users.forEach(user => {
                    const userElement = document.createElement('div');
                    userElement.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200 fade-in';
                    userElement.innerHTML = `
                        <div>
                            <p class="font-semibold text-gray-800">${user.name}</p>
                            <p class="text-sm text-gray-500">${user.email}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button data-id="${user.id}" data-name="${user.name}" data-email="${user.email}" class="edit-btn p-2 text-gray-500 hover:text-indigo-600 transition-colors" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pointer-events-none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button data-id="${user.id}" class="delete-btn p-2 text-gray-500 hover:text-red-600 transition-colors" title="Excluir">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pointer-events-none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </div>
                    `;
                    userList.appendChild(userElement);
                });
            }
        } catch (error) {
            console.error('Falha ao buscar usuários:', error);
            showError('Não foi possível carregar os usuários. Verifique a conexão e o console (F12).');
        } finally {
            showLoading(false);
        }
    };
    
    const showForm = (user = null) => {
        userForm.reset();
        if (user) {
            formTitle.textContent = 'Editar Usuário';
            saveButton.textContent = 'Salvar Alterações';
            userIdInput.value = user.id;
            nameInput.value = user.name;
            emailInput.value = user.email;
        } else {
            formTitle.textContent = 'Adicionar Novo Usuário';
            saveButton.textContent = 'Salvar Usuário';
            userIdInput.value = '';
        }
        formSection.style.display = 'block';
        addUserButtonContainer.style.display = 'none';
        nameInput.focus();
    };

    const hideForm = () => {
        formSection.style.display = 'none';
        addUserButtonContainer.style.display = 'block';
        userForm.reset();
        userIdInput.value = '';
    };

    // --- EVENT LISTENERS ---

    // ==================================================================
    // ALTERAÇÃO PRINCIPAL AQUI: Lógica de submissão atualizada
    // ==================================================================
    userForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        saveButton.disabled = true;
        saveButton.textContent = 'Salvando...';

        const formData = new FormData(userForm);
        const userId = userIdInput.value; // Pega o ID do input hidden

        try {
            let result;
            if (userId) {
                // Se tem ID, ATUALIZA
                result = await api.updateUser(userId, formData);
            } else {
                // Se não tem ID, CRIA
                result = await api.createUser(formData);
            }
            
            Swal.fire({ icon: 'success', title: 'Sucesso!', text: result.message, timer: 2000, showConfirmButton: false });
            hideForm();
            await renderUsers();

        } catch (error) {
            console.error('Falha ao salvar usuário:', error);
            Swal.fire({ icon: 'error', title: 'Oops...', text: error.message || 'Não foi possível salvar o usuário.' });
        } finally {
            saveButton.disabled = false;
            // Restaura o texto correto do botão
            saveButton.textContent = userIdInput.value ? 'Salvar Alterações' : 'Salvar Usuário';
        }
    });

    userList.addEventListener('click', async (e) => {
        const editButton = e.target.closest('.edit-btn');
        const deleteButton = e.target.closest('.delete-btn');

        if (editButton) {
            // Usando os data attributes para pegar os dados de forma segura
            const user = {
                id: editButton.dataset.id,
                name: editButton.dataset.name,
                email: editButton.dataset.email
            };
            showForm(user);
        }

        if (deleteButton) {
            const userIdToDelete = deleteButton.dataset.id;
            Swal.fire({
                title: 'Você tem certeza?', text: "Você não poderá reverter isso!", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, deletar!', cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const deleteResult = await api.deleteUser(userIdToDelete);
                        Swal.fire('Deletado!', deleteResult.message, 'success');
                        await renderUsers();
                    } catch (error) {
                         console.error('Falha ao deletar usuário:', error);
                         Swal.fire('Erro!', error.message || 'Não foi possível deletar o usuário.', 'error');
                    }
                }
            });
        }
    });

    document.getElementById('add-user-button').addEventListener('click', () => showForm());
    document.getElementById('cancel-button').addEventListener('click', hideForm);

    // Inicia a aplicação carregando os usuários
    renderUsers();
});