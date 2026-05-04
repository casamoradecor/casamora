@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endpush

@section('content')
<main class="dashboard-container">
    <aside class="dashboard-nav">
        <a href="{{ route('dashboard') }}" style="text-decoration: underline;">RESUMO</a>
        <a href="{{ route('pedidos.index') }}">MEUS PEDIDOS</a>
        <a href="{{ route('enderecos.index') }}">ENDEREÇOS</a>
        <a href="{{ route('perfil.edit') }}">EDITAR PERFIL</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout" style="font-family: var(--font-base),sans-serif">SAIR DA CONTA</button>
        </form>
    </aside>

    <section class="dashboard-content">
        <h2 style="font-family: 'Poppins', serif">MEU PERFIL</h2>

        @if(session('sucesso'))
            <p style="color: green; font-weight: bold;">✓ {{ session('sucesso') }}</p>
        @endif

        <form action="{{ route('perfil.update') }}" method="POST" class="form-perfil">
            @csrf @method('PUT')

            <div class="form-group">
                <label>NOME COMPLETO</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-perfil" required>
                @error('name') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>E-MAIL</label>
                <input type="email" name="email" id="email_perfil" value="{{ old('email', $user->email) }}" class="input-perfil" required>
                @error('email') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>CPF</label>
                <input type="text" name="cpf" id="cpf_perfil" value="{{ old('cpf', $user->cpf) }}" class="input-perfil" maxlength="14" required>
                @error('cpf') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: bold; font-size: 0.8rem; margin: 30px 0 20px;">
                <input type="checkbox" id="toggle-password-fields" name="alterar_senha" {{ old('alterar_senha') ? 'checked' : '' }}>
                DESEJA ALTERAR A SENHA?
            </label>

            <div id="password-fields" class="password-grid" style="{{ old('alterar_senha') ? 'display: grid;' : 'display: none;' }}">
                <div class="form-group full-width">
                    <label>SENHA ATUAL</label>
                    <div class="password-wrapper">
                        <input type="password" name="current_password" class="input-perfil">
                        <button type="button" class="toggle-password">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    @error('current_password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>NOVA SENHA</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="new_password" class="input-perfil">
                        <button type="button" class="toggle-password">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    <ul class="password-requirements">
                        <li id="req-length" class="invalid">Mínimo de 8 caracteres</li>
                        <li id="req-number" class="invalid">Pelo menos um número</li>
                        <li id="req-special" class="invalid">Um caractere especial</li>
                        <li id="req-match" class="invalid">As senhas não coincidem</li>
                    </ul>
                    @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>CONFIRMAR SENHA</label>
                    <div class="password-wrapper">
                        <input type="password" name="password_confirmation" id="confirm_password" class="input-perfil">
                        <button type="button" class="toggle-password">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-salvar">SALVAR ALTERAÇÕES</button>
        </form>
    </section>
</main>
@endsection

@push('js')
    <script src="{{ asset('js/ProfileManager.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new ProfileManager();
        });
    </script>
@endpush
