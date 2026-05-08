@extends('layouts.app')

@section('title', 'CASA MORÁ — EDITAR')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/novo-produto.css') }}">
@endpush

@section('header_class', 'admin-hidden')

@section('content')
    <div class="admin-wrapper admin-body-novo">

        @include('sidebar.menu_lateral')

        <main class="admin-main">
            <div class="novo-container">
                <button style="padding: 10px " class="mobile-menu-toggle" onclick="toggleAdminMenu()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <form id="form-edita-produto" action="{{ route('admin.produto.update', $produto->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- FOTOS --}}
                    <div class="card-form">
                        <label class="label-mora">foto atual</label>
                        <img src="{{ asset('storage/' . $produto->imagem) }}" class="edit-img-preview">

                        <div class="upload-placeholder">
                            <input type="file" name="imagem">
                            <p class="info-helper-text">deixe vazio para manter a foto atual</p>
                        </div>
                        @error('imagem')
                        <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- IDENTIFICAÇÃO --}}
                    <div class="form-grid-2">
                        <div class="card-form">
                            <label class="label-mora">código do produto (sku)</label>
                            <input type="text" name="codigo" value="{{ old('codigo', $produto->codigo) }}" class="input-mora" required>
                            @error('codigo')
                            <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="card-form">
                            <label class="label-mora">nome do item</label>
                            <input type="text" name="nome" value="{{ old('nome', $produto->nome) }}" class="input-mora" required>
                            @error('nome')
                            <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- DESCRIÇÃO --}}
                    <div class="card-form">
                        <label class="label-mora">descrição detalhada</label>
                        <textarea name="descricao" rows="5" class="input-mora" placeholder="DETALHES TÉCNICOS E ESTILO...">{{ old('descricao', $produto->descricao) }}</textarea>
                        @error('descricao')
                        <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- FINANCEIRO E ESTOQUE --}}
                    <div class="form-grid-2">
                        <div class="card-form">
                            <label class="label-mora">valor (r$)</label>
                            <input type="number" step="0.01" min="0" name="preco" value="{{ old('preco', $produto->preco) }}" class="input-mora" required>
                            @error('preco')
                            <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="card-form">
                            <label class="label-mora">estoque atual</label>
                            <input type="number" min="0" name="estoque" value="{{ old('estoque', $produto->estoque) }}" class="input-mora" required>
                            @error('estoque')
                            <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- LOGÍSTICA E FRETE --}}
                    <div style="margin: 20px 0 10px 10px;">
                        <span class="label-mora" style="opacity: 0.6; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px;">
                            Dimensões para cálculo de Frete
                        </span>
                    </div>

                    <div class="form-grid-2">
                        <div class="card-form">
                            <label class="label-mora">peso (kg) — ex: 0.800</label>
                            <input type="number" step="0.001" min="0" name="peso" value="{{ old('peso', $produto->peso) }}" class="input-mora" required>
                            @error('peso')
                            <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="card-form">
                            <label class="label-mora">largura (cm)</label>
                            <input type="number" min="0" name="largura" value="{{ old('largura', $produto->largura) }}" class="input-mora" required>
                            @error('largura')
                            <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="card-form">
                            <label class="label-mora">altura (cm)</label>
                            <input type="number" min="0" name="altura" value="{{ old('altura', $produto->altura) }}" class="input-mora" required>
                            @error('altura')
                            <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="card-form">
                            <label class="label-mora">comprimento (cm)</label>
                            <input type="number" min="0" name="comprimento" value="{{ old('comprimento', $produto->comprimento) }}" class="input-mora" required>
                            @error('comprimento')
                            <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- CATEGORIA E DESTAQUE --}}
                    <div class="card-form">
                        <div class="form-row-between">
                            <div class="form-col-45">
                                <label class="label-mora">categoria</label>
                                <select name="categoria_id" class="input-mora" required>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categoria_id', $produto->categoria_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-col-45">
                                <label class="label-mora">status de destaque</label>
                                <label class="switch-wrapper" for="check_lancamento">
                                    <input type="checkbox" name="lancamento" value="1" id="check_lancamento" class="switch-input" {{ old('lancamento', $produto->lancamento) ? 'checked' : '' }}>
                                    <div class="switch-button"></div>
                                    <span class="label-mora label-switch">definir como lançamento</span>
                                </label>
                                @error('lancamento')
                                <span style="color: #d9534f; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-marrom">
                        salvar alterações
                    </button>
                </form>
            </div>
        </main>
    </div>

    <div id="modal-confirmacao" class="modal-mora-overlay">
        <div class="modal-mora-content">
            <div class="modal-mora-header">
                <span class="label-mora" style="font-size: 1.2rem;">Confirmar Alterações</span>
                <p style="font-size: 0.8rem; color: #777; margin-top: 5px;">Por favor, revise as modificações antes de salvar.</p>
            </div>

            <div id="modal-dados-produto" style="padding: 15px; background: #fff; border: 1px solid #E0DCD3;">
            </div>

            <div class="modal-mora-footer">
                <button type="button" class="btn btn-branco" onclick="fecharModal()">Voltar</button>
                <button type="button" class="btn btn-marrom" onclick="enviarFormulario()">Atualizar Produto</button>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            let formPendente = null;

            function toggleAdminMenu() {
                const sidebar = document.querySelector('.admin-sidebar');
                sidebar.classList.toggle('active');
            }

            document.addEventListener('click', function(event) {
                const sidebar = document.querySelector('.admin-sidebar');
                const toggleBtn = document.querySelector('.mobile-menu-toggle');

                if (sidebar && sidebar.classList.contains('active')) {
                    if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                        sidebar.classList.remove('active');
                    }
                }
            });

            // Lógica do Modal
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('form-edita-produto');

                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    formPendente = form;

                    const nome = form.querySelector('input[name="nome"]').value;
                    const precoInput = form.querySelector('input[name="preco"]').value;
                    const peso = form.querySelector('input[name="peso"]').value;
                    const largura = form.querySelector('input[name="largura"]').value;
                    const altura = form.querySelector('input[name="altura"]').value;
                    const comp = form.querySelector('input[name="comprimento"]').value;
                    const estoque = form.querySelector('input[name="estoque"]').value;

                    const precoFloat = parseFloat(precoInput.replace(',', '.'));
                    const precoFormatado = isNaN(precoFloat) ? 'R$ 0,00' : precoFloat.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

                    const modalBody = document.getElementById('modal-dados-produto');
                    modalBody.innerHTML = `
                        <div class="modal-resumo-linha"><strong>Item:</strong> ${nome}</div>
                        <div class="modal-resumo-linha" style="text-transform: uppercase"><strong>Valor:</strong> ${precoFormatado}</div>
                        <div class="modal-resumo-linha"><strong>Estoque Atual:</strong> ${estoque} un.</div>
                        <hr style="border: 0; border-top: 1px solid #E0DCD3; margin: 15px 0;">
                        <span class="label-mora" style="opacity: 0.6; font-size: 0.7rem; display:block; margin-bottom: 8px;">Dados Logísticos</span>
                        <div class="modal-resumo-linha"><strong>Peso:</strong> ${peso} kg</div>
                        <div class="modal-resumo-linha"><strong>Dimensões:</strong> ${largura}cm x ${altura}cm x ${comp}cm</div>
                    `;

                    document.getElementById('modal-confirmacao').style.display = 'flex';
                });
            });

            function fecharModal() {
                document.getElementById('modal-confirmacao').style.display = 'none';
            }

            function enviarFormulario() {
                if(formPendente) {
                    formPendente.submit();
                }
            }
        </script>
    @endpush
@endsection
