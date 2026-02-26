@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endpush

@section('content')
<main class="dashboard-container">
    <aside class="dashboard-nav">       
        <a href="{{ route('dashboard') }}">RESUMO</a>
        <a href="{{ route('pedidos.index') }}">MEUS PEDIDOS</a>
        <a href="{{ route('perfil.edit') }}" style="text-decoration: underline;">MEU PERFIL</a>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">SAIR DA CONTA</button>
        </form>
    </aside>

    <section class="dashboard-content">
        <h2>MEU PERFIL</h2>

        @if(session('sucesso'))
            <p style="color: green; font-weight: bold;">✓ {{ session('sucesso') }}</p>
        @endif

        <form action="{{ route('perfil.update') }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label>NOME</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-perfil">
                @error('name') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>E-MAIL</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-perfil">
                @error('email') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>CPF</label>
                <input type="text" name="cpf" id="cpf_perfil" value="{{ old('cpf', $user->cpf) }}" class="input-perfil" maxlength="14">
                @error('cpf') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: bold; font-size: 0.8rem; margin-top: 20px;">
                <input type="checkbox" id="toggle-password" name="alterar_senha" {{ old('alterar_senha') ? 'checked' : '' }}>
                DESEJA ALTERAR A SENHA?
            </label>

            <div id="password-fields" class="password-grid" style="{{ old('alterar_senha') ? 'display: grid;' : '' }}">
                <div class="form-group full-width">
                    <label>SENHA ATUAL</label>
                    <input type="password" name="current_password" class="input-perfil">
                    @error('current_password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>NOVA SENHA</label>
                    <input type="password" name="password" class="input-perfil">
                    @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>CONFIRMAR SENHA</label>
                    <input type="password" name="password_confirmation" class="input-perfil">
                </div>
            </div>

            <button type="submit" class="btn-salvar">SALVAR ALTERAÇÕES</button>
        </form>
    </section>
</main>

<script>
    const checkbox = document.getElementById('toggle-password');
    const fields = document.getElementById('password-fields');

    checkbox.addEventListener('change', function() {
        fields.style.display = this.checked ? 'grid' : 'none';
    });

    document.getElementById('cpf_perfil').addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 3 && v.length <= 6) v = v.replace(/(\d{3})(\d+)/, "$1.$2");
        else if (v.length > 6 && v.length <= 9) v = v.replace(/(\d{3})(\d{3})(\d+)/, "$1.$2.$3");
        else if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d+)/, "$1.$2.$3-$4");
        e.target.value = v.slice(0, 14);
    });
</script>
@endsection