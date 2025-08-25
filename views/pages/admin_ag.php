<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="p-6 bg-slate-800 text-white">
        <h1 class="text-2xl sm:text-3xl font-bold">Painel de Administração</h1>
        <p class="mt-1 text-slate-300">Gerencie os horários, profissionais e serviços.</p>
    </div>

    <div class="p-6 md:p-8 space-y-8">
        <div class="bg-gray-50 p-5 rounded-lg border">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">1. Adicionar Novo Profissional</h2>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <div class="sm:col-span-1">
                    <label for="prof-nome" class="block text-sm font-medium text-gray-700">Nome</label>
                    <input type="text" id="prof-nome" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" placeholder="Nome completo">
                </div>
                <div class="sm:col-span-1">
                    <label for="prof-email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="prof-email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" placeholder="email@exemplo.com">
                </div>
                <div class="sm:col-span-1">
                    <label for="prof-telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                    <input type="tel" id="prof-telefone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" placeholder="(XX) XXXXX-XXXX">
                </div>
                <div>
                    <button id="btn-add-profissional" class="w-full bg-teal-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-teal-700 transition-colors">
                        Adicionar
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-lg border">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">2. Adicionar Novo Serviço</h2>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <div class="sm:col-span-1">
                    <label for="serv-nome" class="block text-sm font-medium text-gray-700">Nome do Serviço</label>
                    <input type="text" id="serv-nome" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" placeholder="Ex: Corte de Cabelo">
                </div>
                <div class="sm:col-span-1">
                    <label for="serv-duracao" class="block text-sm font-medium text-gray-700">Duração (min)</label>
                    <input type="number" id="serv-duracao" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" placeholder="Ex: 30">
                </div>
                <div class="sm:col-span-1">
                    <label for="serv-preco" class="block text-sm font-medium text-gray-700">Preço (R$)</label>
                    <input type="number" step="0.01" id="serv-preco" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" placeholder="Ex: 50.00">
                </div>
                <div>
                    <button id="btn-add-servico" class="w-full bg-cyan-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-cyan-700 transition-colors">
                        Adicionar
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-lg border">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">3. Vincular Serviço a Profissional</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="link-profissional" class="block text-sm font-medium text-gray-700">Profissional</label>
                    <select id="link-profissional" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
                        <option value="">Selecione...</option>
                        <?php foreach ($profissionais as $p): ?>
                            <option value="<?= htmlspecialchars($p['id']) ?>"><?= htmlspecialchars($p['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="link-servico" class="block text-sm font-medium text-gray-700">Serviço</label>
                    <select id="link-servico" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
                        <option value="">Selecione...</option>
                        <?php foreach ($servicos as $s): ?>
                            <option value="<?= htmlspecialchars($s['id']) ?>"><?= htmlspecialchars($s['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button id="btn-link-service" class="w-full bg-orange-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-orange-600 transition-colors">
                        Vincular
                    </button>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 p-5 rounded-lg border">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">4. Adicionar Novo Horário de Disponibilidade</h2>
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end">
                <div class="sm:col-span-1">
                    <label for="admin-profissional" class="block text-sm font-medium text-gray-700">Profissional</label>
                    <select id="admin-profissional" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
                         <option value="">Selecione...</option>
                        <?php foreach ($profissionais as $p): ?>
                            <option value="<?= htmlspecialchars($p['id']) ?>" data-nome="<?= htmlspecialchars($p['nome']) ?>">
                                <?= htmlspecialchars($p['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label for="admin-servico" class="block text-sm font-medium text-gray-700">Serviço</label>
                    <select id="admin-servico" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" disabled>
                        <option value="">Selecione um profissional...</option>
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label for="admin-date" class="block text-sm font-medium text-gray-700">Data</label>
                    <input type="date" id="admin-date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
                </div>
                <div class="sm:col-span-1">
                    <label for="admin-time" class="block text-sm font-medium text-gray-700">Hora</label>
                    <input type="time" id="admin-time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
                </div>
                <div class="sm:col-span-1">
                    <button id="btn-add-availability" class="w-full bg-indigo-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors">
                        Adicionar Horário
                    </button>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Horários do Dia</h2>
            <div id="horarios-admin-container" class="space-y-3">
                <p class="text-center text-gray-500">Selecione uma data para ver os horários.</p>
            </div>
        </div>
    </div>
</div>
</main>