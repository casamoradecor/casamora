@extends('layouts.app')

@section('title', 'Newsletter — Casa MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/newsletter-admin.css') }}">
@endpush

@section('header_class', 'admin-hidden')

@section('content')
    <div class="admin-wrapper admin-body">
        @include('sidebar.menu_lateral')

        <main class="admin-main">
            <header class="admin-header-list">
                <button class="mobile-menu-toggle" onclick="toggleAdminMenu()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 style="font-family: 'Poppins', serif">Newsletter</h1>
                <span class="header-info-label">Gestão de Inscritos</span>
            </header>

            <div class="admin-header-list" style="flex-direction: row; margin-bottom: 30px;">
                <button type="button" class="btn btn-marrom" onclick="abrirModalEmail()">
                    enviar e-mail para todos
                </button>
            </div>

            @if(session('sucesso'))
                <div class="alert-success-mora" style="margin-bottom: 20px;">
                    {{ session('sucesso') }}
                </div>
            @endif

            <div class="table-card">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>e-mail</th>
                        <th>data de inscrição</th>
                        <th>status</th>
                        <th>ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($inscritos as $inscrito)
                        <tr>
                            <td>
                                <span class="sku-label">#{{ $inscrito->id }}</span>
                            </td>
                            <td>
                                <div class="product-info">
                                    <strong style="text-transform: lowercase;">{{ $inscrito->email }}</strong>
                                </div>
                            </td>
                            <td>{{ $inscrito->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="btn-launch {{ $inscrito->active ? 'active' : '' }}">
                                    {{ $inscrito->active ? 'ativo' : 'inativo' }}
                                </span>
                            </td>
                            <td>
                                <div class="actions-flex">
                                    <button type="button" class="btn-action-minimal trash" onclick="abrirModalExclusao('{{ $inscrito->id }}', '{{ $inscrito->email }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="modalEmail" class="modal-overlay">
        <div class="modal-box" style="max-width: 1000px; width: 95%;">
            <h2 style="font-family: 'Poppins', serif; text-align: center; margin-bottom: 20px;">
                CRIAR NEWSLETTER INTELIGENTE <i class="fa-solid fa-wand-magic-sparkles" style="color: #D4AF37;"></i>
            </h2>

            <form action="{{ route('admin.newsletter.enviar') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 30px; flex-wrap: wrap;">

                    <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; gap: 15px;">

                        <div style="background: #fdfaf8; border: 1px solid #eee; padding: 15px; border-radius: 8px;">
                            <label style="font-size: 0.7rem; font-weight: 800; color: #4B3621;">O QUE A IA DEVE ESCREVER?</label>
                            <textarea id="promptIA" rows="3" placeholder="Ex: Crie um e-mail curto e elegante avisando sobre 20% de desconto em poltronas de couro..." style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px; font-family: sans-serif; resize: vertical;"></textarea>

                            <button type="button" id="btnGerarIA" onclick="gerarComIA()" class="btn btn-marrom" style="width: 100%; margin-top: 10px">
                                <i class="fa-solid fa-robot"></i> GERAR CONTEÚDO COM IA
                            </button>
                        </div>

                        <div>
                            <label style="font-size: 0.7rem; font-weight: 800; color: #888;">ASSUNTO DO E-MAIL</label>
                            <input type="text" name="assunto" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px;">
                        </div>

                        <div style="flex-grow: 1; display: flex; flex-direction: column;">
                            <label style="font-size: 0.7rem; font-weight: 800; color: #888;">CONTEÚDO (HTML PERMITIDO)</label>
                            <textarea name="conteudo" id="conteudoEmail" rows="8" required style="width: 100%; flex-grow: 1; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px; font-family: sans-serif; resize: vertical;"></textarea>
                        </div>
                    </div>

                    <div style="flex: 1; min-width: 300px; background: #f4f4f4; border-radius: 8px; padding: 10px; display: flex; flex-direction: column;">
                        <label style="font-size: 0.7rem; font-weight: 800; color: #888; text-align: center; margin-bottom: 10px;">PRÉ-VISUALIZAÇÃO AO VIVO</label>
                        <iframe id="previewIframe" style="width: 100%; flex-grow: 1; min-height: 400px; border: 1px solid #ddd; background: white; border-radius: 4px;"></iframe>
                    </div>

                </div>

                <div class="actions-flex" style="justify-content: center; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                    <button type="button" onclick="fecharModalEmail()" class="btn btn-branco">CANCELAR</button>
                    <button type="submit" class="btn btn-marrom">DISPARAR AGORA</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalDelete" class="modal-overlay">
        <div class="modal-box">
            <h2>confirmar remoção</h2>
            <p>remover o e-mail da lista:<br>
                <strong id="nomeItemModal" class="modal-item-highlight"></strong>
            </p>
            <div class="actions-flex" style="justify-content: center; margin-top: 20px;">
                <button onclick="fecharModal()" class="btn btn-branco">cancelar</button>
                <form id="formDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-marrom">remover agora</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalEmail() { document.getElementById('modalEmail').style.display = 'flex'; }
        function fecharModalEmail() { document.getElementById('modalEmail').style.display = 'none'; }

        function abrirModalExclusao(id, email) {
            document.getElementById('nomeItemModal').innerText = email;
            document.getElementById('formDelete').action = "/admin/newsletter/" + id;
            document.getElementById('modalDelete').style.display = 'flex';
        }
        function fecharModal() { document.getElementById('modalDelete').style.display = 'none'; }
    </script>
    <script>
        // Função para abrir e fechar o menu no mobile
        function toggleAdminMenu() {
            const sidebar = document.querySelector('.admin-sidebar');
            sidebar.classList.toggle('active');
        }

        // Fecha o menu automaticamente se o usuário clicar fora dele
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.admin-sidebar');
            const toggleBtn = document.querySelector('.mobile-menu-toggle');

            // Verifica se o clique foi fora da sidebar e do botão de abrir
            if (sidebar && sidebar.classList.contains('active')) {
                if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
    <script>
        // Lógica da Pré-visualização ao vivo
        const textareaConteudo = document.getElementById('conteudoEmail');
        const iframePreview = document.getElementById('previewIframe');

        // Sempre que o usuário (ou a IA) digitar algo no campo de conteúdo, atualiza o iframe
        textareaConteudo.addEventListener('input', atualizarPreview);

        function atualizarPreview() {
            const htmlUsuario = textareaConteudo.value;

            // Simula a casca do seu template de e-mail para o preview ser fiel
            const estruturaEmail = `
                <!DOCTYPE html>
                <html>
                <body style="background-color: #fdfaf8; font-family: 'Poppins', sans-serif; padding: 20px; margin: 0;">
                    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #eee;">
                        <h1 style="text-align: center; color: #4B3621; letter-spacing: 3px; font-size: 24px; margin-top: 0;">CASA MORÁ</h1>
                        <div style="line-height: 1.6; color: #555; font-size: 14px;">
                            ${htmlUsuario ? htmlUsuario : '<p style="color: #aaa; text-align: center;">O conteúdo do seu e-mail aparecerá aqui...</p>'}
                        </div>
                    </div>
                </body>
                </html>
            `;

            // Escreve o HTML dentro do iframe
            const doc = iframePreview.contentWindow.document;
            doc.open();
            doc.write(estruturaEmail);
            doc.close();
        }

        // Abre o modal e já limpa/atualiza o preview
        function abrirModalEmail() {
            document.getElementById('modalEmail').style.display = 'flex';
            atualizarPreview();
        }

        function fecharModalEmail() {
            document.getElementById('modalEmail').style.display = 'none';
        }

        async function gerarComIA() {
            const prompt = document.getElementById('promptIA').value;
            const btn = document.getElementById('btnGerarIA');

            if(!prompt) {
                alert("Por favor, digite o que a IA deve escrever no campo acima.");
                return;
            }
            const textoOriginal = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> PENSANDO...';
            btn.disabled = true;

            try {

                const response = await fetch("{{ route('admin.newsletter.gerar-ia') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ prompt: prompt })
                });

                const data = await response.json();

                if (data.sucesso) {
                    textareaConteudo.value = data.conteudo;

                    textareaConteudo.dispatchEvent(new Event('input'));
                } else {
                    alert("Erro ao gerar conteúdo: " + data.erro);
                }
            } catch (error) {
                console.error("Erro na requisição:", error);
                alert("Erro de conexão com o servidor. Tente novamente.");
            } finally {
                btn.innerHTML = textoOriginal;
                btn.disabled = false;
            }
        }
    </script>
@endsection
