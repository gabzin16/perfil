// Controle do modo de edição
let editMode = false;

// Estados e cidades do Brasil
const estados = {
    'AC': ['Rio Branco', 'Cruzeiro do Sul', 'Sena Madureira'],
    'AL': ['Maceió', 'Arapiraca', 'Palmeira dos Índios'],
    'AP': ['Macapá', 'Santana', 'Laranjal do Jari'],
    'AM': ['Manaus', 'Parintins', 'Itacoatiara'],
    'BA': ['Salvador', 'Feira de Santana', 'Vitória da Conquista'],
    'CE': ['Fortaleza', 'Caucaia', 'Juazeiro do Norte'],
    'DF': ['Brasília', 'Ceilândia', 'Taguatinga'],
    'ES': ['Vitória', 'Vila Velha', 'Serra'],
    'GO': ['Goiânia', 'Aparecida de Goiânia', 'Anápolis'],
    'MA': ['São Luís', 'Imperatriz', 'Timon'],
    'MT': ['Cuiabá', 'Várzea Grande', 'Rondonópolis'],
    'MS': ['Campo Grande', 'Dourados', 'Três Lagoas'],
    'MG': ['Belo Horizonte', 'Uberlândia', 'Contagem'],
    'PA': ['Belém', 'Ananindeua', 'Santarém'],
    'PB': ['João Pessoa', 'Campina Grande', 'Santa Rita'],
    'PR': ['Curitiba', 'Londrina', 'Maringá'],
    'PE': ['Recife', 'Jaboatão dos Guararapes', 'Olinda'],
    'PI': ['Teresina', 'Parnaíba', 'Picos'],
    'RJ': ['Rio de Janeiro', 'São Gonçalo', 'Duque de Caxias'],
    'RN': ['Natal', 'Mossoró', 'Parnamirim'],
    'RS': ['Porto Alegre', 'Caxias do Sul', 'Pelotas'],
    'RO': ['Porto Velho', 'Ji-Paraná', 'Ariquemes'],
    'RR': ['Boa Vista', 'Caracaraí', 'Rorainópolis'],
    'SC': ['Florianópolis', 'Joinville', 'Blumenau'],
    'SP': ['São Paulo', 'Guarulhos', 'Santo André', 'Campinas', 'Santos'],
    'SE': ['Aracaju', 'Nossa Senhora do Socorro', 'Lagarto'],
    'TO': ['Palmas', 'Araguaína', 'Gurupi']
};

// Função para carregar estados
function loadEstados() {
    const selectEstado = document.getElementById('estado');
    if (!selectEstado) return;

    // Limpa o select
    selectEstado.innerHTML = '<option value="">Selecione</option>';

    // Adiciona os estados
    Object.keys(estados).forEach(uf => {
        const option = document.createElement('option');
        option.value = uf;
        option.textContent = uf;
        selectEstado.appendChild(option);
    });
}

// Função para carregar cidades
function loadCidades(uf) {
    const selectCidade = document.getElementById('cidade');
    if (!selectCidade) return;

    // Limpa o select
    selectCidade.innerHTML = '<option value="">Selecione</option>';

    // Se tiver um estado selecionado, carrega as cidades
    if (uf && estados[uf]) {
        estados[uf].forEach(cidade => {
            const option = document.createElement('option');
            option.value = cidade;
            option.textContent = cidade;
            selectCidade.appendChild(option);
        });
    }
}

// Função para inicializar selects de estado/cidade
function initializeLocationSelects() {
    const selectEstado = document.getElementById('estado');
    if (!selectEstado) return;

    // Carrega estados
    loadEstados();

    // Adiciona evento de mudança de estado
    selectEstado.addEventListener('change', function() {
        loadCidades(this.value);
    });

    // Se já tiver um estado selecionado, carrega suas cidades
    if (selectEstado.value) {
        loadCidades(selectEstado.value);
    }
}

// Função para alternar entre modo visualização e edição
function toggleEditMode() {
    const form = document.getElementById('profile-form');
    const inputs = form.querySelectorAll('input:not([type="file"]), select');
    const saveBtn = document.getElementById('save-btn');
    
    editMode = !editMode;
    
    inputs.forEach(input => {
        input.disabled = !editMode;
    });
    
    saveBtn.textContent = editMode ? 'Salvar' : 'Editar';
}

// Função para mostrar/esconder formulários dinâmicos
function toggleForm(section) {
    const forms = document.querySelectorAll('.info-input');
    forms.forEach(form => {
        if (form.id !== `${section}-form`) {
            form.classList.remove('active');
        }
    });
    
    const currentForm = document.getElementById(`${section}-form`);
    if (currentForm) {
        currentForm.classList.toggle('active');
    }
}

// Função para exibir mensagens de feedback
function showMessage(message, type) {
    const existingMessage = document.querySelector('.message');
    if (existingMessage) {
        existingMessage.remove();
    }

    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    const mainContainer = document.querySelector('.main-container');
    mainContainer.insertAdjacentElement('beforebegin', messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

// Função para salvar o perfil principal
async function saveProfile(event) {
    event.preventDefault();
    
    if (!editMode && event.target.textContent === 'Editar') {
        toggleEditMode();
        return;
    }
    
    const form = document.getElementById('profile-form');
    const formData = new FormData(form);
    
    try {
        const response = await fetch('handlers/profile_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            toggleEditMode();
            loadProfileData();
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        showMessage('Erro ao salvar o perfil', 'error');
        console.error('Erro:', error);
    }
}

// Função para enviar dados de seções dinâmicas
async function submitSectionData(section) {
    const input = document.querySelector(`#${section}-form input`);
    if (!input) return;

    const value = input.value.trim();
    if (!value) {
        showMessage('Por favor, preencha o campo', 'error');
        return;
    }

    const data = {
        tipo: section,
        valor: value
    };
    
    try {
        const response = await fetch(`handlers/sections.php?section=${section}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            input.value = '';
            loadSectionItems(section);
            toggleForm(section);
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        showMessage('Erro ao processar requisição', 'error');
        console.error('Erro:', error);
    }
}

// Função para carregar itens de cada seção
async function loadSectionItems(section) {
    try {
        const response = await fetch(`handlers/sections.php?section=${section}&action=list`);
        const result = await response.json();
        
        const container = document.getElementById(`${section}-items`);
        if (container && result.success) {
            container.innerHTML = result.items.map(item => {
                switch(section) {
                    case 'escolaridade':
                        return `
                            <div class="education-item">
                                <div class="item-content">
                                    <h4>${item.nivel} em ${item.curso}</h4>
                                    <p>${item.instituicao} (${item.ano_inicio}${item.em_andamento ? ' - Atual' : item.ano_conclusao ? ` - ${item.ano_conclusao}` : ''})</p>
                                </div>
                                <button onclick="deleteItem('${section}', ${item.id})" class="delete-btn">×</button>
                            </div>`;
                    
                    case 'competencias':
                        return `
                            <div class="skill-item">
                                <div class="item-content">
                                    <h4>${item.competencia}</h4>
                                    <p>Nível: ${item.nivel}</p>
                                </div>
                                <button onclick="deleteItem('${section}', ${item.id})" class="delete-btn">×</button>
                            </div>`;
                    
                    case 'certificacoes':
                        return `
                            <div class="certification-item">
                                <div class="item-content">
                                    <h4>${item.nome}</h4>
                                    <p>${item.instituicao} (${item.ano_conclusao})</p>
                                    ${item.link_verificacao ? `<a href="${item.link_verificacao}" target="_blank">Verificar</a>` : ''}
                                </div>
                                <button onclick="deleteItem('${section}', ${item.id})" class="delete-btn">×</button>
                            </div>`;
                    
                    case 'idiomas':
                        return `
                            <div class="language-item">
                                <div class="item-content">
                                    <h4>${item.idioma}</h4>
                                    <p>Nível: ${item.nivel}</p>
                                    ${item.certificacao ? `<p>Certificação: ${item.certificacao}</p>` : ''}
                                </div>
                                <button onclick="deleteItem('${section}', ${item.id})" class="delete-btn">×</button>
                            </div>`;
                }
            }).join('');
        }
    } catch (error) {
        showMessage('Erro ao carregar itens', 'error');
        console.error('Erro:', error);
    }
}

// Função para deletar item
async function deleteItem(section, id) {
    if (!confirm('Tem certeza que deseja excluir este item?')) {
        return;
    }

    try {
        const response = await fetch(`handlers/sections.php?section=${section}&action=delete&id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('Item excluído com sucesso', 'success');
            loadSectionItems(section);
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        showMessage('Erro ao excluir item', 'error');
        console.error('Erro:', error);
    }
}

// Função para carregar dados do perfil
async function loadProfileData() {
    try {
        const response = await fetch('handlers/get_profile.php');
        const data = await response.json();
        
        if (data.success) {
            // Preenche os campos do formulário
            Object.keys(data.profile).forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    if (input.type === 'file') {
                        return;
                    }
                    input.value = data.profile[field] || '';

                    // Se for estado, carrega as cidades correspondentes
                    if (field === 'estado' && data.profile[field]) {
                        loadCidades(data.profile[field]);
                    }
                }
            });

            // Atualiza a foto do perfil
            const photoPreview = document.getElementById('photo-preview');
            if (photoPreview && data.profile.foto_perfil) {
                photoPreview.src = data.profile.foto_perfil;
            }

            // Atualiza o label do currículo
            const curriculoLabel = document.querySelector('[for="curriculo-input"]');
            if (curriculoLabel && data.profile.curriculo_pdf) {
                curriculoLabel.textContent = `Currículo atual: ${data.profile.curriculo_pdf.split('/').pop()}`;
            }

            // Carrega os itens das seções
            ['escolaridade', 'competencias', 'certificacoes', 'idiomas'].forEach(section => {
                loadSectionItems(section);
            });

            // Define modo de edição
            if (data.profile.perfil_completo) {
                const saveBtn = document.getElementById('save-btn');
                if (saveBtn) {
                    saveBtn.textContent = 'Editar';
                    toggleEditMode();
                }
            }
        }
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
        showMessage('Erro ao carregar dados do perfil', 'error');
    }
}

// Preview de imagem
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            if (preview) {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Máscaras para campos
function maskPhone(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    
    if (value.length > 7) {
        value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
    } else if (value.length > 2) {
        value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
    }
    
    e.target.value = value;
}

function maskCEP(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 8) value = value.slice(0, 8);
    
    if (value.length > 5) {
        value = value.replace(/^(\d{5})(\d{3}).*/, '$1-$2');
    }
    
    e.target.value = value;
}

document.addEventListener('DOMContentLoaded', () => {
    // Carrega dados iniciais
    loadProfileData();
    
    // Inicializa selects de estado/cidade
    initializeLocationSelects();

    // Event listener para o formulário principal
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', saveProfile);
    }

    // Event listeners para botões de adicionar
    document.querySelectorAll('.adicionar-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const section = btn.closest('.info-input').id.replace('-form', '');
            submitSectionData(section);
        });
    });

    // Event listener para foto
    const fotoInput = document.getElementById('foto-input');
    if (fotoInput) {
        fotoInput.addEventListener('change', function() {
            previewImage(this);
        });
    }

    // Event listener para currículo
    const curriculoInput = document.getElementById('curriculo-input');
    if (curriculoInput) {
        curriculoInput.addEventListener('change', function() {
            const label = this.nextElementSibling;
            if (this.files.length > 0) {
                label.textContent = `Arquivo selecionado: ${this.files[0].name}`;
            } else {
                label.textContent = 'Anexar currículo (PDF)';
            }
        });
    }

    // Event listeners para máscaras
    const phoneInput = document.getElementById('telefone');
    if (phoneInput) {
        phoneInput.addEventListener('input', maskPhone);
    }

    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', maskCEP);
    }

    // Event listener para estado
    const estadoSelect = document.getElementById('estado');
    if (estadoSelect) {
        estadoSelect.addEventListener('change', function() {
            loadCidades(this.value);
        });
    }
});