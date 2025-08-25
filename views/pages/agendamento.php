<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 bg-indigo-700 text-white">
                <h1 class="text-2xl sm:text-3xl font-bold">Agende seu Horário</h1>
                <p class="mt-1 text-indigo-200">Simples, rápido e fácil.</p>
            </div>

            <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">1. Selecione a Data</h2>
                    <div id="calendar-container" class="bg-gray-50 p-4 rounded-md">
                        <div id="calendar-header" class="flex justify-between items-center mb-4">
                            <button id="prev-month" class="px-3 py-1 bg-gray-200 rounded-md hover:bg-gray-300">&lt;</button>
                            <h3 id="month-year" class="font-semibold"></h3>
                            <button id="next-month" class="px-3 py-1 bg-gray-200 rounded-md hover:bg-gray-300">&gt;</button>
                        </div>
                        <div id="calendar-grid" class="grid grid-cols-7 gap-1 text-center text-sm">
                            </div>
                    </div>
                </div>

                <div>
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">2. Escolha o Horário</h2>
                        
                        <div class="mt-2 sm:mt-0">
                            <label for="profissional-select" class="text-sm font-medium text-gray-600 mr-2">Profissional:</label>
                            <select id="profissional-select" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                <option value="all">Todos</option>
                                <?php if (isset($profissionais) && !empty($profissionais)): ?>
                                    <?php foreach ($profissionais as $profissional): ?>
                                        <option value="<?= htmlspecialchars($profissional['id']) ?>">
                                            <?= htmlspecialchars($profissional['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div id="horarios-container" class="bg-gray-50 p-4 rounded-md min-h-[150px]">
                        <p class="text-center text-gray-500">Selecione uma data no calendário.</p>
                    </div>
                </div>
            </div>
        </div>
        </main>