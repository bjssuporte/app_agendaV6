<div class="container mx-auto p-4 md:p-8 max-w-4xl">
    <header class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Gerenciamento de Usuários</h1>
        <p class="text-gray-600 mt-2">Aplicação CRUD conectada a um backend PHP via API.</p>
    </header>

    <main class="bg-white rounded-xl shadow-lg p-6 md:p-8">

        <!-- Seção do Formulário (para adicionar/editar) -->
        <div id="form-section" class="mb-8" style="display: none;">
            <h2 id="form-title" class="text-2xl font-semibold mb-4">Adicionar Novo Usuário</h2>
            <form id="user-form" class="space-y-4">
                <!-- ================================================================== -->
                <!-- ALTERAÇÃO PRINCIPAL AQUI: Adicionado o atributo name="id"         -->
                <!-- Agora o ID será enviado junto com o formulário.                  -->
                <!-- ================================================================== -->
                <input type="hidden" id="user-id" name="id">

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
             <div id="error-message" class="hidden text-center py-4 bg-red-100 text-red-700 rounded-lg"></div>
        </div>
    </main>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div id="delete-modal" class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full text-center">
        <h3 class="text-lg font-bold">Confirmar Exclusão</h3>
        <p class="my-4 text-gray-600">Você tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.</p>
        <div class="flex justify-center space-x-4">
            <button id="cancel-delete" class="px-6 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Cancelar</button>
            <button id="confirm-delete" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Excluir</button>
        </div>
    </div>
</div>
