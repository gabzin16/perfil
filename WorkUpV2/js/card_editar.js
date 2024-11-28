// FOTO
// Função para carregar a pré-visualização da imagem
const inputImagem = document.getElementById('image-upload');
const previewImagem = document.getElementById('profile-image');

inputImagem.addEventListener('change', function(e) {
  if (e.target.files && e.target.files[0]) {
    const reader = new FileReader();

    reader.onload = function(e) {
      previewImagem.src = e.target.result;
    }
    reader.readAsDataURL(e.target.files[0]);
  }
});

// _______________________________________________________________
// CIDADE E ESTADOS
const estadoSelect = document.getElementById('estado');
const cidadeSelect = document.getElementById('cidade');

// Carrega estados do arquivo JSON
fetch('estados.json')
  .then(response => response.json())
  .then(estados => {
    estados.forEach(estado => {
      const option = document.createElement('option');
      option.value = estado.id;
      option.text = estado.nome;
      estadosSelect.add(option);
    });
  });

// Evento de mudança no select de estados
estadoSelect.addEventListener('change', () => {
  const estadoId = parseInt(estadoSelect.value, 10); // Converte para número
  cidadeSelect.innerHTML = '<option value="">Selecione</option>';
  cidadeSelect.disabled = true;

  if (estadoId) {
    // Carrega cidades do arquivo JSON
    fetch('cidades.json')
      .then(response => response.json())
      .then(cidades => {
        // Filtra cidades do estado selecionado
        const cidadesDoEstado = cidades.filter(cidade => cidade.estado_id === estadoId);

        cidadesSelect.disabled = false;
        cidadesDoEstado.forEach(cidade => {
          const option = document.createElement('option');
          option.value = cidade.id;
          option.text = cidade.nome;
          cidadesSelect.add(option);
        });
      });
  }
});

// ______________________________________________________________________
// CARDS
function editarCampo(elemento) {
    const cardContent = elemento.parentElement.nextElementSibling;
    const campo = cardContent.dataset.campo;
    const conteudoOriginal = cardContent.innerHTML;
  
    // Substitui o conteúdo por um campo de input para edição
    cardContent.innerHTML = `
      <textarea>${conteudoOriginal}</textarea>
      <button onclick="salvarEdicao(this, '${campo}')">Salvar</button>
      <button onclick="cancelarEdicao(this, '${conteudoOriginal}')">Cancelar</button>
    `;
  }
  
  function salvarEdicao(elemento, campo) {
    const novoConteudo = elemento.previousElementSibling.value;
    const cardContent = elemento.parentElement;
  
    // Lógica para enviar os dados para o servidor (AJAX)
    console.log("Salvando:", campo, "-", novoConteudo);
  
    // Atualiza o conteúdo do card
    cardContent.innerHTML = novoConteudo;
  }
  
  function cancelarEdicao(elemento, conteudoOriginal) {
    const cardContent = elemento.parentElement;
    cardContent.innerHTML = conteudoOriginal;
  }

