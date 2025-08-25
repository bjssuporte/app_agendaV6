/**
 * agendamento_grupo.js
 * * Este script gerencia a interface de agendamento para uma página de grupo específica.
 * Ele lê um 'slug' de grupo da página para garantir que todas as chamadas de API
 * busquem apenas dados (datas, horários) pertencentes àquele grupo.
 */

// A URL base da API continua a mesma.
const urlApi = 'api/agendamento';

class AgendamentoGrupoApp {
    constructor() {
        this.availableDates = new Set();
        this.selectedDate = null;
        this.selectedProfissionalId = 'all'; 

        // NOVO: Lê o slug (identificador) do grupo a partir de um campo hidden no HTML
        const grupoSlugElement = document.getElementById('grupo-slug');
        if (!grupoSlugElement) {
            console.error("Erro crítico: Elemento #grupo-slug não encontrado na página. O agendamento não pode funcionar.");
            return;
        }
        this.grupoSlug = grupoSlugElement.value;
        
        // Controla o mês e ano que estão sendo exibidos
        const today = new Date();
        this.displayMonth = today.getMonth();
        this.displayYear = today.getFullYear();

        this.init();
    }

    async init() {
        await this.fetchAvailableDates();
        this.renderCalendar();
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        document.getElementById('prev-month').addEventListener('click', () => this.changeMonth(-1));
        document.getElementById('next-month').addEventListener('click', () => this.changeMonth(1));
        
        const profissionalSelect = document.getElementById('profissional-select');
        if (profissionalSelect) {
            profissionalSelect.addEventListener('change', (e) => {
                this.selectedProfissionalId = e.target.value;
                this.selectedDate = null;
                document.getElementById('horarios-container').innerHTML = '<p class="text-center text-gray-500">Selecione uma data no calendário.</p>';
                this.init();
            });
        }
    }
    
    changeMonth(direction) {
        this.displayMonth += direction;
        if (this.displayMonth < 0) {
            this.displayMonth = 11;
            this.displayYear--;
        } else if (this.displayMonth > 11) {
            this.displayMonth = 0;
            this.displayYear++;
        }
        this.selectedDate = null;
        document.getElementById('horarios-container').innerHTML = '<p class="text-center text-gray-500">Selecione uma data no calendário.</p>';
        this.renderCalendar();
    }

    /**
     * MODIFICADO: A URL da API agora é dinâmica, baseada no GRUPO e no profissional selecionado.
     */
    async fetchAvailableDates() {
        // Constrói a URL base para o grupo específico
        const groupApiUrl = `${urlApi}/g/${this.grupoSlug}`;
        let fetchUrl = `${groupApiUrl}/datas`;

        // Se um profissional for selecionado, adiciona o filtro (lógica mantida)
        // Assumindo que o backend suporta a rota: /api/agendamento/g/{slug}/datas/profissional/{id}
        if (this.selectedProfissionalId !== 'all') {
            fetchUrl = `${groupApiUrl}/datas/profissional/${this.selectedProfissionalId}`;
        }

        try {
            const response = await fetch(fetchUrl);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const dates = await response.json();
            this.availableDates = new Set(dates);
        } catch (error) {
            console.error('Erro ao buscar datas do grupo:', error);
            this.availableDates = new Set();
            document.getElementById('calendar-container').innerHTML = '<p class="text-center text-red-500">Não foi possível carregar as datas disponíveis.</p>';
        }
    }
    
    /**
     * MODIFICADO: A URL da API também é dinâmica para o grupo aqui.
     */
    async fetchHorarios(dateStr) {
        const container = document.getElementById('horarios-container');
        container.innerHTML = '<p class="text-center text-gray-500">Carregando...</p>';

        const groupApiUrl = `${urlApi}/g/${this.grupoSlug}`;
        let fetchUrl = `${groupApiUrl}/horarios/${dateStr}`;

        if (this.selectedProfissionalId !== 'all') {
            fetchUrl = `${groupApiUrl}/horarios/${dateStr}/profissional/${this.selectedProfissionalId}`;
        }
        
        try {
            const response = await fetch(fetchUrl);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            this.renderHorarios(data);
        } catch (error) {
            console.error('Erro ao carregar horários do grupo:', error);
            container.innerHTML = '<p class="text-center text-red-500">Erro ao carregar horários.</p>';
        }
    }
    
    // NENHUMA MUDANÇA NECESSÁRIA EM renderCalendar, renderHorarios, showConfirmationModal, bookSlot
    // Eles são independentes da URL usada para buscar os dados.

    renderCalendar() {
        const grid = document.getElementById('calendar-grid');
        grid.innerHTML = ''; 

        const monthYearHeader = document.getElementById('month-year');
        const displayDate = new Date(this.displayYear, this.displayMonth);
        monthYearHeader.textContent = displayDate.toLocaleString('pt-BR', { month: 'long', year: 'numeric' });

        ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'].forEach(day => grid.innerHTML += `<div class="font-medium text-gray-500">${day}</div>`);

        const firstDayOfMonth = new Date(this.displayYear, this.displayMonth, 1).getDay();
        const daysInMonth = new Date(this.displayYear, this.displayMonth + 1, 0).getDate();

        for (let i = 0; i < firstDayOfMonth; i++) { grid.innerHTML += '<div></div>'; }

        const today = new Date();
        const todayDate = new Date(Date.UTC(today.getFullYear(), today.getMonth(), today.getDate()));

        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${this.displayYear}-${String(this.displayMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayBtn = document.createElement('button');
            dayBtn.textContent = day;
            let classes = 'calendar-day h-9 w-9 flex items-center justify-center rounded-full transition-colors duration-200 ';

            const [year, month, dayNum] = dateStr.split('-').map(Number);
            const dayDate = new Date(Date.UTC(year, month - 1, dayNum));

            if (this.availableDates.has(dateStr) && dayDate >= todayDate) {
                classes += 'bg-blue-100 text-blue-700 hover:bg-blue-300 cursor-pointer';
                dayBtn.onclick = () => {
                    this.selectedDate = dateStr;
                    this.renderCalendar(); 
                    this.fetchHorarios(dateStr);
                };
            } else {
                classes += 'text-gray-400 cursor-not-allowed';
                dayBtn.disabled = true;
            }

            if (this.selectedDate === dateStr) {
                classes += ' bg-indigo-600 text-white font-bold';
            }

            dayBtn.className = classes;
            grid.appendChild(dayBtn);
        }
    }

    renderHorarios(data) {
        const container = document.getElementById('horarios-container');
        container.innerHTML = '';
        
        if ((Array.isArray(data) && data.length === 0) || (!Array.isArray(data) && Object.keys(data).length === 0)) {
            container.innerHTML = '<p class="text-center text-gray-500">Nenhum horário disponível para esta data.</p>';
            return;
        }

        if (!Array.isArray(data)) {
            const professionalNames = Object.keys(data);
            professionalNames.forEach(name => {
                const professionalBlock = document.createElement('div');
                professionalBlock.className = 'mb-4';

                const title = document.createElement('h4');
                title.className = 'font-bold text-gray-700 mb-2';
                title.textContent = name;
                professionalBlock.appendChild(title);

                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-3 sm:grid-cols-4 gap-3';
                
                data[name].forEach(slot => {
                    grid.appendChild(this.createHorarioButton(slot));
                });

                professionalBlock.appendChild(grid);
                container.appendChild(professionalBlock);
            });
        } 
        else {
            const grid = document.createElement('div');
            grid.className = 'grid grid-cols-3 sm:grid-cols-4 gap-3';
            data.forEach(slot => {
                grid.appendChild(this.createHorarioButton(slot));
            });
            container.appendChild(grid);
        }
    }
    
    createHorarioButton(slot) {
        const button = document.createElement('button');
        const horaFormatada = slot.hora.substring(0, 5);
        button.textContent = horaFormatada;
        button.className = 'p-2 border border-indigo-600 text-indigo-600 font-semibold rounded-md hover:bg-indigo-600 hover:text-white transition-colors duration-200';
        button.onclick = () => this.showConfirmationModal(slot.id, horaFormatada);
        return button;
    }

    showConfirmationModal(slotId, hora) {
        Swal.fire({
            title: 'Confirme seus Dados',
            html: `
                <p class="mb-4">Agendando para <strong>${new Date(this.selectedDate + 'T00:00:00').toLocaleDateString('pt-BR', { timeZone: 'UTC' })}</strong> às <strong>${hora}</strong>.</p>
                <input type="text" id="swal-nome" class="swal2-input" placeholder="Seu nome completo">
                <input type="email" id="swal-email" class="swal2-input" placeholder="Seu melhor e-mail">
                <input type="telefone" id="swal-telefone" class="swal2-input" placeholder="telefone">
            `,
            confirmButtonText: 'Confirmar Agendamento',
            focusConfirm: false,
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const nome = Swal.getPopup().querySelector('#swal-nome').value;
                const email = Swal.getPopup().querySelector('#swal-email').value;
                const telefone = Swal.getPopup().querySelector('#swal-telefone').value;
                if (!nome || !email || !telefone) {
                    Swal.showValidationMessage(`Por favor, preencha nome, e-mail e telefone`);
                }
                return { nome, email, telefone };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this.bookSlot(slotId, result.value.nome, result.value.email, result.value.telefone);
            }
        });
    }

    async bookSlot(slotId, nome, email, telefone) {
        Swal.fire({ title: 'Processando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        const formData = new FormData();
        formData.append('slotId', slotId);
        formData.append('nome', nome);
        formData.append('email', email);
        formData.append('telefone', telefone);

        try {
            // A rota de agendamento é global e não precisa do slug do grupo, pois usa o ID único do slot.
            const response = await fetch(`${urlApi}/agendar`, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message);

            Swal.fire({
                icon: 'success',
                title: 'Agendamento Confirmado!',
                html: `<div class="text-left"><p class="mb-4">Um resumo foi enviado para <strong>${result.summary.email}</strong>.</p>
                    <ul class="list-disc list-inside bg-gray-100 p-4 rounded-md">
                        <li><strong>Cliente:</strong> ${result.summary.cliente}</li>
                        <li><strong>Telefone:</strong> ${result.summary.telefone}</li>
                        <li><strong>Profissional:</strong> ${result.summary.profissional}</li>
                        <li><strong>Data:</strong> ${result.summary.data}</li>
                        <li><strong>Hora:</strong> ${result.summary.hora}</li>
                    </ul>
                </div>`,
                confirmButtonText: 'Ótimo!'
            }).then(() => {
                this.selectedDate = null;
                document.getElementById('horarios-container').innerHTML = '<p class="text-center text-gray-500">Selecione uma data no calendário.</p>';
                this.init();
            });

        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Erro!', text: error.message || 'Não foi possível concluir o agendamento.' });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new AgendamentoGrupoApp();
});