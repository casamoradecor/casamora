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
        <div class="modal-box modal-newsletter-box">
            <h2 class="modal-newsletter-title">
                CRIAR NEWSLETTER INTELIGENTE <i class="fa-solid fa-wand-magic-sparkles" style="color: #D4AF37;"></i>
            </h2>

            <form action="{{ route('admin.newsletter.enviar') }}" method="POST" class="modal-newsletter-form">
                @csrf

                <div class="modal-email-content">

                    <div class="modal-email-col modal-email-inputs">

                        <div class="ia-block">
                            <label>O QUE A IA DEVE ESCREVER?</label>
                            <textarea id="promptIA" rows="3" placeholder="Ex: Crie um e-mail curto e elegante avisando sobre 20% de desconto em poltronas de couro..."></textarea>
                            <button type="button" id="btnGerarIA" onclick="gerarComIA()" class="btn btn-marrom">
                                <i class="fa-solid fa-robot"></i> GERAR CONTEÚDO COM IA
                            </button>
                        </div>

                        <div class="input-block">
                            <label>ASSUNTO DO E-MAIL</label>
                            <input type="text" name="assunto" required>
                        </div>

                        <div class="input-block content-block">
                            <label>CONTEÚDO (HTML PERMITIDO)</label>
                            <textarea name="conteudo" id="conteudoEmail" rows="8" required></textarea>
                        </div>
                    </div>

                    <div class="modal-email-col modal-email-preview">
                        <label>PRÉ-VISUALIZAÇÃO AO VIVO</label>
                        <iframe id="previewIframe"></iframe>
                    </div>

                </div>

                <div class="actions-flex modal-newsletter-actions">
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
        const textareaConteudo = document.getElementById('conteudoEmail');
        const iframePreview = document.getElementById('previewIframe');
        let isSyncing = false;

        function inicializarIframe() {
            const doc = iframePreview.contentWindow.document;
            const estruturaEmail = `
                <!DOCTYPE html>
                <html>
                <body style="background-color: #fdfaf8; font-family: 'Poppins', sans-serif; padding: 20px; margin: 0; cursor: text;">
                    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #eee;">
                        <h1 style="text-align: center; color: #4B3621; letter-spacing: 3px; font-size: 24px; margin-top: 0; user-select: none;">CASA MORÁ</h1>

                        <div id="editor-visual" contenteditable="true" style="line-height: 1.6; color: #555; font-size: 14px; min-height: 200px; outline: none;">
                            ${textareaConteudo.value || '<p style="color: #aaa; text-align: center;">Clique aqui e comece a digitar o seu e-mail, ou peça para a IA gerar...</p>'}
                        </div>
                    </div>
                </body>
                </html>
            `;
            doc.open();
            doc.write(estruturaEmail);
            doc.close();

            const editorVisual = doc.getElementById('editor-visual');
            editorVisual.addEventListener('input', function() {
                if (!isSyncing) {
                    isSyncing = true;
                    textareaConteudo.value = editorVisual.innerHTML;
                    isSyncing = false;
                }
            });

            editorVisual.addEventListener('focus', function() {
                if(this.innerHTML.includes('Clique aqui e comece a digitar')) {
                    this.innerHTML = '';
                }
            });
        }

        textareaConteudo.addEventListener('input', function() {
            if (!isSyncing) {
                isSyncing = true;
                const doc = iframePreview.contentWindow.document;
                const editorVisual = doc.getElementById('editor-visual');
                if (editorVisual) {
                    editorVisual.innerHTML = textareaConteudo.value;
                }
                isSyncing = false;
            }
        });

        function abrirModalEmail() {
            document.getElementById('modalEmail').style.display = 'flex';
            const doc = iframePreview.contentWindow.document;
            if (!doc.getElementById('editor-visual')) {
                inicializarIframe();
            }
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
                    const inputAssunto = document.querySelector('input[name="assunto"]');
                    if (inputAssunto && data.assunto) {
                        inputAssunto.value = data.assunto;
                    }

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