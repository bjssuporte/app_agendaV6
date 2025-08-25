const urlApiAdmin = 'api/agendamento/admin';
const urlApiProfissionais = 'api/profissionais';
const urlApiServicos = 'api/servicos'; // Nova URL

class AdminApp {
    constructor() {
        this.baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '';
        this.selectedDate = new Date().toISOString().split('T')[0];

        // Seletores para Adicionar Horário
        this.profissionalSelect = document.getElementById('admin-profissional');
        this.servicoSelect = document.getElementById('admin-servico'); // NOVO
        this.dateInput = document.getElementById('admin-date');
        this.timeInput = document.getElementById('admin-time');
        this.addButton = document.getElementById('btn-add-availability');
        this.container = document.getElementById('horarios-admin-container');

        // Seletores para Adicionar Profissional
        this.profNomeInput = document.getElementById('prof-nome');
        this.profEmailInput = document.getElementById('prof-email');
        this.profTelefoneInput = document.getElementById('prof-telefone');
        this.addProfissionalButton = document.getElementById('btn-add-profissional');

        // NOVO: Seletores para Adicionar Serviço
        this.servNomeInput = document.getElementById('serv-nome');
        this.servDuracaoInput = document.getElementById('serv-duracao');
        this.servPrecoInput = document.getElementById('serv-preco');
        this.addServicoButton = document.getElementById('btn-add-servico');

        // NOVO: Seletores para Vincular Serviço
        this.linkProfissionalSelect = document.getElementById('link-profissional');
        this.linkServicoSelect = document.getElementById('link-servico');
        this.linkServiceButton = document.getElementById('btn-link-service');


        this.init();
    }

    init() {
        this.dateInput.value = this.selectedDate;
        this.dateInput.min = new Date().toISOString().split('T')[0];

        // Event Listeners
        this.dateInput.addEventListener('change', (e) => {
            this.selectedDate = e.target.value;
            this.fetchAdminHorarios();
        });

        this.addButton.addEventListener('click', () => this.addHorario());
        this.addProfissionalButton.addEventListener('click', () => this.addProfissional());

        // NOVO: Event Listeners
        this.addServicoButton.addEventListener('click', () => this.addServico());
        this.linkServiceButton.addEventListener('click', () => this.linkService());
        this.profissionalSelect.addEventListener('change', () => this.fetchServicesForProfessional());

        this.fetchAdminHorarios();
    }

    // ... (fetchAdminHorarios e deleteHorario não precisam de alteração no corpo, mas renderAdminHorarios sim) ...
    async fetchAdminHorarios() {
        this.container.innerHTML = '<p class="text-gray-500 text-sm">Carregando...</p>';
        try {
            const response = await fetch(`${this.baseUrl}${urlApiAdmin}/horarios/${this.selectedDate}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const horarios = await response.json();
            this.renderAdminHorarios(horarios);
        } catch (error) {
            console.error('Erro ao carregar horários (admin):', error);
            this.container.innerHTML = '<p class="text-red-500 text-sm">Erro ao carregar horários.</p>';
        }
    }

    renderAdminHorarios(horarios) {
        this.container.innerHTML = '';
        if (horarios.length === 0) {
            this.container.innerHTML = '<p class="text-gray-500 text-sm">Nenhum horário cadastrado para esta data.</p>';
            return;
        }

        horarios.forEach(slot => {
            const wrapper = document.createElement('div');
            wrapper.className = 'bg-white rounded-md shadow-sm overflow-hidden';
            const mainRow = document.createElement('div');
            mainRow.className = 'flex justify-between items-center p-3 gap-2';
            const horaFormatada = slot.hora.substring(0, 5);

            // ===== ALTERAÇÃO AQUI =====
            // Lógica para montar o texto de exibição de forma mais robusta.
            const parts = [slot.profissionalNome || 'Profissional'];
            if (slot.servicoNome) {
                parts.push(`(${slot.servicoNome})`); // Adiciona o nome do serviço
            }
            if (slot.status === 'agendado' && slot.clienteNome) {
                parts.push(slot.clienteNome); // Adiciona o nome do cliente se agendado
            }
            const displayText = parts.join(' - ');
            // ===== FIM DA ALTERAÇÃO =====


            mainRow.innerHTML = `
            <div class="flex-grow">
                <span class="font-mono text-indigo-700 font-semibold">${horaFormatada}</span>
                <p class="text-xs text-gray-500">${displayText}</p>
            </div>
        `;

            if (slot.status === 'disponivel') {
                mainRow.innerHTML += `<span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">Disponível</span>`;
                const button = document.createElement('button');
                button.innerHTML = `&times;`;
                button.className = 'text-red-500 hover:text-red-800 font-bold text-lg leading-none ml-2';
                button.title = 'Remover horário';
                button.onclick = () => this.deleteHorario(slot.id);
                mainRow.appendChild(button);
            } else {
                const detailsId = `details-${slot.id}`;
                const toggleButton = document.createElement('button');
                toggleButton.className = 'text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded-full hover:bg-red-200 transition-colors flex items-center gap-1';
                toggleButton.innerHTML = `Agendado <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg>`;
                toggleButton.onclick = () => {
                    const detailsEl = document.getElementById(detailsId);
                    const icon = toggleButton.querySelector('svg');
                    const isHidden = detailsEl.style.display === 'none';
                    detailsEl.style.display = isHidden ? 'block' : 'none';
                    icon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
                };
                mainRow.appendChild(toggleButton);
                const detailsRow = document.createElement('div');
                detailsRow.id = detailsId;
                detailsRow.className = 'p-3 border-t border-gray-200 bg-gray-50 text-sm';
                detailsRow.style.display = 'none';

                detailsRow.innerHTML = `
                <p><strong>Telefone:</strong> ${slot.clienteTelefone || 'Não informado'}</p>
                <p><strong>Email:</strong> ${slot.clienteEmail || 'Não informado'}</p>
            `;

                wrapper.appendChild(detailsRow);
            }
            wrapper.insertBefore(mainRow, wrapper.firstChild);
            this.container.appendChild(wrapper);
        });
    }
    async deleteHorario(id) {
        const confirmation = await Swal.fire({
            title: 'Tem certeza?',
            text: "Você não poderá reverter isso!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, remover!',
            cancelButtonText: 'Cancelar'
        });

        if (confirmation.isConfirmed) {
            try {
                const response = await fetch(`${this.baseUrl}${urlApiAdmin}/delete/${id}`, { method: 'POST' });
                const result = await response.json();
                if (result.success) {
                    Swal.fire('Removido!', 'O horário foi removido.', 'success');
                    this.fetchAdminHorarios();
                } else {
                    throw new Error(result.message || 'Ocorreu um erro desconhecido.');
                }
            } catch (error) {
                Swal.fire('Erro!', error.message, 'error');
            }
        }
    }


    /**
     * NOVO: Carrega dinamicamente os serviços para o profissional selecionado.
     */
    async fetchServicesForProfessional() {
        const profissionalId = this.profissionalSelect.value;
        this.servicoSelect.innerHTML = '<option value="">Carregando...</option>';
        this.servicoSelect.disabled = true;

        if (!profissionalId) {
            this.servicoSelect.innerHTML = '<option value="">Selecione um profissional...</option>';
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}${urlApiProfissionais}/${profissionalId}/servicos`);
            if (!response.ok) throw new Error('Não foi possível carregar os serviços.');

            const services = await response.json();

            this.servicoSelect.innerHTML = '<option value="">Selecione o serviço...</option>';
            if (services.length > 0) {
                services.forEach(service => {
                    const option = new Option(service.nome, service.id);
                    this.servicoSelect.appendChild(option);
                });
                this.servicoSelect.disabled = false;
            } else {
                this.servicoSelect.innerHTML = '<option value="">Nenhum serviço vinculado</option>';
            }
        } catch (error) {
            console.error('Erro ao buscar serviços:', error);
            this.servicoSelect.innerHTML = '<option value="">Erro ao carregar</option>';
        }
    }

    /**
     * MODIFICADO: Adiciona um horário, agora incluindo o serviço.
     */
    async addHorario() {
        const data = this.dateInput.value;
        const hora = this.timeInput.value;
        const profissionalId = this.profissionalSelect.value;
        const selectedOption = this.profissionalSelect.options[this.profissionalSelect.selectedIndex];
        const profissionalNome = selectedOption ? selectedOption.dataset.nome : '';
        const id_servico = this.servicoSelect.value; // NOVO

        if (!data || !hora || !profissionalId || !id_servico) { // MODIFICADO
            Swal.fire('Atenção', 'Por favor, preencha todos os campos: profissional, serviço, data e hora.', 'warning');
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}${urlApiAdmin}/add`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ data, hora, profissionalId, profissionalNome, id_servico }) // MODIFICADO
            });
            const result = await response.json();

            if (result.success) {
                Swal.fire('Sucesso!', 'Horário adicionado.', 'success');
                this.fetchAdminHorarios();
                this.timeInput.value = '';
            } else {
                throw new Error(result.message || 'Ocorreu um erro desconhecido.');
            }
        } catch (error) {
            Swal.fire('Erro!', error.message, 'error');
        }
    }

    /**
     * Adiciona um profissional (sem modificação)
     */
    async addProfissional() {
        const nome = this.profNomeInput.value.trim();
        const email = this.profEmailInput.value.trim();
        const telefone = this.profTelefoneInput.value.trim();

        if (!nome || !email) {
            Swal.fire('Atenção', 'Nome e e-mail são obrigatórios.', 'warning');
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}${urlApiProfissionais}/add`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nome, email, telefone })
            });

            const result = await response.json();
            if (!response.ok) throw new Error(result.message);

            Swal.fire('Sucesso!', 'Profissional adicionado.', 'success').then(() => {
                window.location.reload(); // Recarrega a página para atualizar as listas
            });

        } catch (error) {
            Swal.fire('Erro!', error.message, 'error');
        }
    }

    /**
     * NOVO: Método para adicionar um serviço
     */
    async addServico() {
        const nome = this.servNomeInput.value.trim();
        const duracao = this.servDuracaoInput.value;
        const preco = this.servPrecoInput.value;

        if (!nome || !duracao || duracao <= 0) {
            Swal.fire('Atenção', 'Nome e duração (maior que 0) são obrigatórios.', 'warning');
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}${urlApiServicos}/add`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nome, duracao, preco })
            });

            const result = await response.json();
            if (!response.ok) throw new Error(result.message);

            Swal.fire('Sucesso!', 'Serviço adicionado.', 'success').then(() => {
                window.location.reload(); // Recarrega para atualizar a lista de serviços
            });

        } catch (error) {
            Swal.fire('Erro!', error.message, 'error');
        }
    }

    /**
     * NOVO: Método para vincular serviço a profissional
     */
    async linkService() {
        const id_profissional = this.linkProfissionalSelect.value;
        const id_servico = this.linkServicoSelect.value;

        if (!id_profissional || !id_servico) {
            Swal.fire('Atenção', 'Selecione um profissional e um serviço para vincular.', 'warning');
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}${urlApiProfissionais}/link-servico`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_profissional, id_servico })
            });

            const result = await response.json();
            if (!response.ok && !result.success) throw new Error(result.message);

            Swal.fire('Sucesso!', result.message || 'Serviço vinculado!', 'success');
        } catch (error) {
            Swal.fire('Erro!', error.message, 'error');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new AdminApp();
});
