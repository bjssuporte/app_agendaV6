<div class="bg-white rounded-xl shadow-lg p-8 max-w-2xl mx-auto">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 text-center">Entre em Contato</h1>
    
    <!-- ATUALIZADO: O 'action' aponta para a nova rota da API e adicionamos um 'id' para facilitar a seleção no JS -->
    <form id="contactForm" action="api/contact/send" method="POST" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Seu Nome</label>
            <input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Seu Email</label>
            <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label for="message" class="block text-sm font-medium text-gray-700">Sua Mensagem</label>
            <textarea id="message" name="message" rows="4" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>
        <div class="text-center">
            <button type="submit" class="w-full md:w-auto bg-indigo-600 text-white font-semibold px-8 py-3 rounded-lg shadow-md hover:bg-indigo-700 transition-all duration-200">
                Enviar Mensagem
            </button>
        </div>
    </form>
</div>