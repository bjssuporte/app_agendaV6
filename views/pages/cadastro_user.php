<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulação de Frontend para CRUD</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Estilo base usando a fonte Inter */
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Pequena animação para a entrada de elementos */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="container mx-auto p-4 md:p-8 max-w-4xl">
        
        <header class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Gerenciamento de Usuários</h1>
            <p class="text-gray-600 mt-2">Esta é uma simulação de frontend. Nenhuma informação é salva em um banco de dados real.</p>
        </header>

        <main class="bg-white rounded-xl shadow-lg p-6 md:p-8">
            
            <!-- Seção do Formulário (para adicionar/editar) -->
            <div id="form-section" class="mb-8" style="display: none;">
                <h2 id="form-title" class="text-2xl font-semibold mb-4">Adicionar Novo Usuário</h2>
                <form id="user-form" class="space-y-4">
                    <input type="hidden" id="user-id">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button type="button" id="cancel-button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">Cancelar</button>
                        <button type="submit" id="save-button" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">Salvar Usuário</button>
                    </div>
                </form>
            </div>

            <!-- Botão para abrir o formulário -->
            <div id="add-user-button-container" class="text-right mb-6">
                 <button id="add-user-button" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg shadow-md hover:bg-indigo-700 transition-all duration-200 transform hover:scale-105">
                    + Adicionar Usuário
                </button>
            </div>

            <!-- Lista/Tabela de Usuários -->
            <div id="user-list-container">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Usuários Cadastrados</h3>
                <div id="user-list" class="space-y-3">
                    <!-- Usuários serão inseridos aqui pelo JS -->
                </div>
                 <div id="loading-spinner" class="text-center py-8" style="display: none;">
                    <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2 text-gray-500">Carregando...</p>
                </div>
            </div>
        </main>
        
        <!-- Modal de Confirmação de Exclusão -->
        <div id="delete-modal" class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full text-center">
                <h3 class="text-lg font-bold">Confirmar Exclusão</h3>
                <p class="my-4 text-gray-600">Você tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.</p>
                <div class="flex justify-center space-x-4">
                    <button id="cancel-delete" class="px-6 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Cancelar</button>
                    <button id="confirm-delete" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Excluir</button>
                </div>
            </div>
        </div>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- ELEMENTOS DO DOM ---
        const userList = document.getElementById('user-list');
        const userForm = document.getElementById('user-form');
        const formSection = document.getElementById('form-section');
        const formTitle = document.getElementById('form-title');
        const userIdInput = document.getElementById('user-id');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const saveButton = document.getElementById('save-button');
        const cancelButton = document.getElementById('cancel-button');
        const addUserButton = document.getElementById('add-user-button');
        const addUserButtonContainer = document.getElementById('add-user-button-container');
        const loadingSpinner = document.getElementById('loading-spinner');
        
        // Modal de exclusão
        const deleteModal = document.getElementById('delete-modal');
        const confirmDeleteButton = document.getElementById('confirm-delete');
        const cancelDeleteButton = document.getElementById('cancel-delete');
        let userIdToDelete = null;

        // --- DADOS FALSOS (SIMULAÇÃO DO BANCO DE DADOS) ---
        let users = [
            { id: 1, name: 'Ada Lovelace', email: 'ada.lovelace@example.com' },
            { id: 2, name: 'Grace Hopper', email: 'grace.hopper@example.com' },
            { id: 3, name: 'Margaret Hamilton', email: 'margaret.hamilton@example.com' }
        ];
        let nextId = 4;

        // --- SIMULAÇÃO DA API (com `fetch` falso) ---
        // Adiciona um pequeno delay para simular uma chamada de rede
        const fakeApi = {
            getUsers: () => new Promise(resolve => setTimeout(() => resolve([...users]), 500)),
            addUser: (user) => new Promise(resolve => {
                setTimeout(() => {
                    const newUser = { id: nextId++, ...user };
                    users.push(newUser);
                    resolve(newUser);
                }, 300);
            }),
            updateUser: (id, updatedData) => new Promise(resolve => {
                setTimeout(() => {
                    users = users.map(u => u.id === id ? { ...u, ...updatedData } : u);
                    resolve(users.find(u => u.id === id));
                }, 300);
            }),
            deleteUser: (id) => new Promise(resolve => {
                setTimeout(() => {
                    users = users.filter(u => u.id !== id);
                    resolve({ success: true });
                }, 300);
            })
        };

        // --- FUNÇÕES DE RENDERIZAÇÃO E LÓGICA ---

        // Renderiza a lista de usuários no DOM
        const renderUsers = async () => {
            showLoading(true);
            userList.innerHTML = '';
            const userArray = await fakeApi.getUsers();
            showLoading(false);

            if (userArray.length === 0) {
                userList.innerHTML = `<p class="text-center text-gray-500 py-4">Nenhum usuário cadastrado.</p>`;
                return;
            }

            userArray.forEach(user => {
                const userElement = document.createElement('div');
                userElement.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200 fade-in';
                userElement.innerHTML = `
                    <div>
                        <p class="font-semibold text-gray-800">${user.name}</p>
                        <p class="text-sm text-gray-500">${user.email}</p>
                    </div>
                    <div class="flex space-x-2">
                        <button data-id="${user.id}" class="edit-btn p-2 text-gray-500 hover:text-indigo-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <button data-id="${user.id}" class="delete-btn p-2 text-gray-500 hover:text-red-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </button>
                    </div>
                `;
                userList.appendChild(userElement);
            });
        };
        
        const showLoading = (isLoading) => {
            loadingSpinner.style.display = isLoading ? 'block' : 'none';
        };

        // Mostra o formulário para adicionar ou editar
        const showForm = (user = null) => {
            if (user) { // Editando
                formTitle.textContent = 'Editar Usuário';
                saveButton.textContent = 'Salvar Alterações';
                userIdInput.value = user.id;
                nameInput.value = user.name;
                emailInput.value = user.email;
            } else { // Adicionando
                formTitle.textContent = 'Adicionar Novo Usuário';
                saveButton.textContent = 'Salvar Usuário';
                userForm.reset();
                userIdInput.value = '';
            }
            formSection.style.display = 'block';
            addUserButtonContainer.style.display = 'none';
            nameInput.focus();
        };

        // Esconde o formulário
        const hideForm = () => {
            formSection.style.display = 'none';
            addUserButtonContainer.style.display = 'block';
            userForm.reset();
        };

        // Manipula o envio do formulário
        userForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = parseInt(userIdInput.value);
            const userData = {
                name: nameInput.value,
                email: emailInput.value
            };

            if (id) { // Atualizar
                await fakeApi.updateUser(id, userData);
            } else { // Adicionar
                await fakeApi.addUser(userData);
            }
            
            hideForm();
            renderUsers();
        });

        // Manipula cliques na lista (para editar ou deletar)
        userList.addEventListener('click', (e) => {
            const editButton = e.target.closest('.edit-btn');
            const deleteButton = e.target.closest('.delete-btn');

            if (editButton) {
                const id = parseInt(editButton.dataset.id);
                const user = users.find(u => u.id === id);
                showForm(user);
            }

            if (deleteButton) {
                userIdToDelete = parseInt(deleteButton.dataset.id);
                deleteModal.style.display = 'flex';
            }
        });
        
        // --- EVENT LISTENERS DOS BOTÕES ---
        addUserButton.addEventListener('click', () => showForm());
        cancelButton.addEventListener('click', hideForm);

        // Listeners do modal de exclusão
        confirmDeleteButton.addEventListener('click', async () => {
            if (userIdToDelete !== null) {
                await fakeApi.deleteUser(userIdToDelete);
                userIdToDelete = null;
                deleteModal.style.display = 'none';
                renderUsers();
            }
        });

        cancelDeleteButton.addEventListener('click', () => {
            userIdToDelete = null;
            deleteModal.style.display = 'none';
        });

        // Renderização inicial
        renderUsers();
    });
    </script>
</body>
</html>
