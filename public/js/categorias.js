/* public/js/categorias.js */

// Adicione estas variáveis ao topo
let categoriaIdParaExcluir = null;
let urlParaExcluir = '';

function abrirModal(id) {
    document.getElementById(id).style.display = 'flex';
}

function fecharModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Troque sua função confirmarExclusao por esta
function confirmarExclusao(url, nome) {
    urlParaExcluir = url;
    document.getElementById('excluirNomeItem').innerText = nome;
    abrirModal('modalExcluir');

    document.getElementById('btnConfirmarExclusao').onclick = function() {
        const form = document.getElementById('formExcluir');
        form.action = urlParaExcluir;
        form.submit();
    };
}

// Troque sua função editarExistente por esta
function editarExistente(id, nomeAtual) {
    document.getElementById('edit_nome').value = nomeAtual;
    document.getElementById('edit_id').value = id;
    abrirModal('modalEditar');
}

function salvarEdicaoModal() {
    const id = document.getElementById('edit_id').value;
    const novoNome = document.getElementById('edit_nome').value;

    if (novoNome.trim() !== "") {
        // Atualiza o hidden field do form principal
        document.querySelector(`input[name="categorias_existentes[${id}][nome]"]`).value = novoNome;
        // Atualiza o texto na tabela
        document.getElementById(`text-nome-existente-${id}`).innerText = novoNome;
        fecharModal('modalEditar');
    }
}

// Mantenha sua função adicionarNaLista mas use a classe td-actions
function adicionarNaLista() {
    const inputNome = document.getElementById('temp_nome');
    const inputDesc = document.getElementById('temp_descricao'); // Captura a descrição
    const nome = inputNome.value.trim();
    const desc = inputDesc.value.trim();

    if (!nome) return alert("por favor, digite o nome.");

    const lista = document.getElementById('listaCategorias');
    const novaLinha = document.createElement('tr');
    const idTemp = Date.now();

    novaLinha.innerHTML = `
        <td>
            <input type="hidden" name="novas_categorias[${idTemp}][nome]" value="${nome}">
            <input type="hidden" name="novas_categorias[${idTemp}][descricao]" value="${desc}">
            <strong class="categoria-nome">${nome}</strong>
        </td>
        <td style="text-align: center;"><span class="status-badge pendente">pendente</span></td>
        <td class="td-actions">
            <button type="button" class="btn-action-minimal btn-trash" onclick="this.closest('tr').remove()">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    `;
    lista.appendChild(novaLinha);

    // Limpa ambos os campos
    inputNome.value = '';
    inputDesc.value = '';
}
