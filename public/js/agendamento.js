// A URL base da API continua a mesma.
const urlApi = 'api/agendamento';

class AgendamentoApp {
    constructor() {
        this.availableDates = new Set();
        this.selectedDate = null;
        this.selectedProfissionalId = 'all'; // NOVO: Controla o filtro

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
        
        // NOVO: Listener para o filtro de profissional
        document.getElementById('profissional-select').addEventListener('change', (e) => {
            this.selectedProfissionalId = e.target.value;
            this.selectedDate = null; // Limpa a data selecionada ao trocar de profissional
            document.getElementById('horarios-container').innerHTML = '<p class="text-center text-gray-500">Selecione uma data no calendário.</p>';
            this.init(); // Reinicia o processo de busca de datas e renderização
        });
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
     * MODIFICADO: A URL da API agora é dinâmica, baseada no profissional selecionado.
     */
    async fetchAvailableDates() {
        let fetchUrl = `${urlApi}/datas`;
        if (this.selectedProfissionalId !== 'all') {
            fetchUrl = `${urlApi}/datas/profissional/${this.selectedProfissionalId}`;
        }

        try {
            const response = await fetch(fetchUrl);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const dates = await response.json();
            this.availableDates = new Set(dates);
        } catch (error) {
            console.error('Erro ao buscar datas:', error);
            this.availableDates = new Set(); // Limpa as datas em caso de erro
            document.getElementById('calendar-container').innerHTML = '<p class="text-center text-red-500">Não foi possível carregar as datas disponíveis.</p>';
        }
    }
    
    /**
     * MODIFICADO: A URL da API também é dinâmica aqui.
     */
    async fetchHorarios(dateStr) {
        const container = document.getElementById('horarios-container');
        container.innerHTML = '<p class="text-center text-gray-500">Carregando...</p>';

        let fetchUrl = `${urlApi}/horarios/${dateStr}`;
        if (this.selectedProfissionalId !== 'all') {
            fetchUrl = `${urlApi}/horarios/${dateStr}/profissional/${this.selectedProfissionalId}`;
        }
        
        try {
            const response = await fetch(fetchUrl);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            this.renderHorarios(data);
        } catch (error) {
            console.error('Erro ao carregar horários:', error);
            container.innerHTML = '<p class="text-center text-red-500">Erro ao carregar horários.</p>';
        }
    }
    
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

    /**
     * GRANDE MODIFICAÇÃO: Renderiza os horários de duas formas diferentes.
     * 1. Se `data` for um array (filtro por profissional), cria uma grade simples de botões.
     * 2. Se `data` for um objeto (visão "Todos"), cria um bloco para cada profissional.
     */
    renderHorarios(data) {
        const container = document.getElementById('horarios-container');
        container.innerHTML = '';
        
        // Verifica se há horários (seja em array ou objeto)
        if ((Array.isArray(data) && data.length === 0) || (!Array.isArray(data) && Object.keys(data).length === 0)) {
            container.innerHTML = '<p class="text-center text-gray-500">Nenhum horário disponível para esta data.</p>';
            return;
        }

        // CASO 1: Visão "Todos" - os dados vêm agrupados em um objeto
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
        // CASO 2: Visão filtrada por profissional - os dados vêm num array simples
        else {
            const grid = document.createElement('div');
            grid.className = 'grid grid-cols-3 sm:grid-cols-4 gap-3';
            data.forEach(slot => {
                grid.appendChild(this.createHorarioButton(slot));
            });
            container.appendChild(grid);
        }
    }
    
    /**
     * NOVO: Função auxiliar para criar os botões de horário, evitando duplicação de código.
     */
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
    new AgendamentoApp();
});