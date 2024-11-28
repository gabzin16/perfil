// Carregando os dados das vagas
let vagas = [];

// Função para carregar os dados do arquivo JSON
async function carregarVagas() {
    try {
        const response = await fetch('vagas.json');
        const data = await response.json();
        vagas = data.vagas;
        exibirVagas(vagas);
    } catch (error) {
        console.error('Erro ao carregar as vagas:', error);
    }
}

// Função para exibir as vagas
function exibirVagas(vagasFiltradas) {
    const container = document.getElementById('job-cards');
    container.innerHTML = '';

    // Adiciona ou remove a classe 'single-card' dependendo do número de vagas
    if (vagasFiltradas.length === 1) {
        container.classList.add('single-card');
    } else {
        container.classList.remove('single-card');
    }

    vagasFiltradas.forEach(vaga => {
        const card = document.createElement('div');
        card.className = 'job-card';
        card.innerHTML = `
            <h3>${vaga.titulo}</h3>
            <p><i class="fa-solid fa-location-dot"></i> ${vaga.localizacao}</p>
            <p><i class="fa-solid fa-building"></i> ${vaga.modalidade}</p>
            <p><i class="fa-solid fa-hourglass-half"></i> ${vaga.tipo}</p>
            <p class="presencial"><i class="fa-solid fa-stopwatch"></i> ${vaga.jornada}</p>
            <button class="acessar-vaga" onclick="redirecionarDetalhamento(${vaga.id})">Acessar vaga</button>
        `;
        container.appendChild(card);
    });
}

// Função para redirecionar para a página de detalhamento da vaga
function redirecionarDetalhamento(idVaga) {
    const vaga = vagas.find(v => v.id === idVaga);
    if (vaga) {
        localStorage.setItem('vagaDetalhada', JSON.stringify(vaga));
        window.location.href = 'detalhamento.html';
    }
}

// Função para filtrar as vagas
function filtrarVagas() {
    const tipoFiltro = document.querySelector('.filter-btn.active').dataset.filter;
    const modalidadeFiltro = document.getElementById('modalidade-select').value;
    const termoBusca = document.getElementById('search-input').value.toLowerCase();

    const vagasFiltradas = vagas.filter(vaga => {
        const passaTipo = tipoFiltro === 'todas' || vaga.tipo === tipoFiltro;
        const passaModalidade = !modalidadeFiltro || vaga.modalidade === modalidadeFiltro;
        const passaBusca = !termoBusca || 
            vaga.titulo.toLowerCase().includes(termoBusca) || 
            vaga.empresa.toLowerCase().includes(termoBusca);

        return passaTipo && passaModalidade && passaBusca;
    });

    exibirVagas(vagasFiltradas);
}

// Event listeners
document.addEventListener('DOMContentLoaded', () => {
    carregarVagas();

    // Filtro por tipo de vaga
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            filtrarVagas();
        });
    });

    // Filtro por modalidade
    document.getElementById('modalidade-select').addEventListener('change', filtrarVagas);

    // Busca por texto
    document.getElementById('search-input').addEventListener('input', filtrarVagas);
});
