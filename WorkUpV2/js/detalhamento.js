// Script para controle da página de detalhamento de vagas

// Variáveis globais para controle do carrossel
let currentIndex = 0;
let itemsPerPage = window.innerWidth <= 768 ? 1 : 4;
let isAnimating = false;

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    initializePage();
    setupCarousel();
    setupEventListeners();
});

/**
 * Inicializa a página carregando dados necessários
 */
function initializePage() {
    loadJobData();
}

/**
 * Carrega os dados da vaga do localStorage
 */
function loadJobData() {
    try {
        const jobData = JSON.parse(localStorage.getItem('vagaDetalhada'));
        if (!jobData) {
            console.error('Dados da vaga não encontrados');
            return;
        }

        updatePageContent(jobData);
    } catch (error) {
        console.error('Erro ao carregar dados da vaga:', error);
    }
}

/**
 * Atualiza o conteúdo da página com os dados da vaga
 * @param {Object} jobData - Dados da vaga
 */
function updatePageContent(jobData) {
    // Atualiza textos
    setElementText('vaga-titulo', jobData.titulo);
    setElementText('empresa-nome', jobData.empresa);
    setElementText('localizacao', jobData.localizacao);
    setElementText('modalidade', jobData.modalidade);

    // Atualiza logo da empresa
    setupCompanyLogo(jobData);
}

/**
 * Define o texto de um elemento se ele existir
 * @param {string} elementId - ID do elemento
 * @param {string} text - Texto a ser definido
 */
function setElementText(elementId, text) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = text;
    }
}

/**
 * Configura a logo da empresa
 * @param {Object} jobData - Dados da vaga
 */
function setupCompanyLogo(jobData) {
    const logoElement = document.getElementById('empresa-logo');
    if (logoElement) {
        logoElement.src = jobData.imagem;
        logoElement.alt = `Logo ${jobData.empresa}`;
        logoElement.loading = 'lazy';
        logoElement.onerror = () => {
            console.warn('Erro ao carregar logo da empresa');
            logoElement.src = 'img/empresa-default.png';
        };
    }
}

/**
 * Configura o carrossel
 */
function setupCarousel() {
    const carousel = document.querySelector('.carousel-wrapper');
    if (!carousel) return;

    updateCarouselDisplay();
}

/**
 * Configura os event listeners
 */
function setupEventListeners() {
    // Listeners para botões do carrossel
    setupCarouselButtons();
    
    // Listener para redimensionamento da janela
    setupResizeListener();
    
    // Listeners para navegação por teclado
    setupKeyboardNavigation();
    
    // Listeners para eventos de touch
    setupTouchEvents();
}

/**
 * Configura os botões do carrossel
 */
function setupCarouselButtons() {
    const prevButton = document.querySelector('.carousel-button.prev');
    const nextButton = document.querySelector('.carousel-button.next');

    if (prevButton) {
        prevButton.addEventListener('click', () => moveCarousel('prev'));
    }
    if (nextButton) {
        nextButton.addEventListener('click', () => moveCarousel('next'));
    }
}

/**
 * Configura o listener de redimensionamento
 */
function setupResizeListener() {
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const newItemsPerPage = window.innerWidth <= 768 ? 1 : 4;
            if (newItemsPerPage !== itemsPerPage) {
                itemsPerPage = newItemsPerPage;
                currentIndex = 0;
                updateCarouselDisplay();
            }
        }, 150);
    });
}

/**
 * Configura a navegação por teclado
 */
function setupKeyboardNavigation() {
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            moveCarousel('prev');
        } else if (e.key === 'ArrowRight') {
            moveCarousel('next');
        }
    });
}

/**
 * Configura eventos de touch para o carrossel
 */
function setupTouchEvents() {
    const carousel = document.querySelector('.carousel-wrapper');
    if (!carousel) return;

    let touchStartX = 0;
    let touchEndX = 0;

    carousel.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });

    carousel.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].clientX;
        const swipeDistance = touchStartX - touchEndX;

        if (Math.abs(swipeDistance) > 50) {
            moveCarousel(swipeDistance > 0 ? 'next' : 'prev');
        }
    }, { passive: true });
}

/**
 * Move o carrossel
 * @param {string} direction - Direção do movimento ('prev' ou 'next')
 */
function moveCarousel(direction) {
    if (isAnimating) return;
    
    const carouselItems = document.querySelectorAll('.carousel-item');
    const maxIndex = Math.max(0, carouselItems.length - itemsPerPage);

    isAnimating = true;

    if (direction === 'prev') {
        currentIndex = currentIndex > 0 ? currentIndex - 1 : maxIndex;
    } else {
        currentIndex = currentIndex < maxIndex ? currentIndex + 1 : 0;
    }

    updateCarouselDisplay();

    setTimeout(() => {
        isAnimating = false;
    }, 500);
}

/**
 * Atualiza a exibição do carrossel
 */
function updateCarouselDisplay() {
    const wrapper = document.querySelector('.carousel-wrapper');
    if (!wrapper) return;

    const slideWidth = 100 / itemsPerPage;
    const translateX = -currentIndex * slideWidth;
    wrapper.style.transform = `translateX(${translateX}%)`;
}

/**
 * Redireciona para a página de vagas
 */
function voltarParaVagas() {
    window.location.href = 'vagas.html';
}

/**
 * Inicia o processo de cadastro
 */
function cadastrarVaga() {
    console.log('Iniciando processo de cadastro...');
    // Implementar lógica de cadastro aqui
}

// Configuração de tratamento de erros global
window.onerror = function(msg, url, line) {
    console.error(`Erro: ${msg}\nURL: ${url}\nLinha: ${line}`);
    return false;
};