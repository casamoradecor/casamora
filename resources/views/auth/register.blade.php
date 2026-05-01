@extends('layouts.app')

@section('title', 'Casa MORÁ — Criar Conta')

@section('header_class', 'scrolled')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
    <main class="login-page">
        <div class="login-split">
            <div class="login-image">
                <img src="{{ asset('assets/vasomora.png') }}" alt="Ambiente Casa MORÁ">
            </div>

            <div class="login-content" style="padding: 40px 10%;">
                <h2 style="margin-bottom: 20px;">CRIAR CONTA</h2>

                <form class="login-form" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="input-group">
                        <label for="name">NOME COMPLETO</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>

                    <div class="input-group">
                        <label for="email">E-MAIL</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <div class="input-group" style="flex: 1; min-width: 140px;">
                            <label for="cpf">CPF</label>
                            <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00">
                            @error('cpf') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group" style="flex: 1; min-width: 140px;">
                            <label for="telefone">TELEFONE</label>
                            <input type="text" id="telefone" maxlength="15" name="telefone" value="{{ old('telefone') }}" placeholder="(00) 00000-0000">
                            @error('telefone') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- CAMPO: SENHA COM OLHINHO --}}
                    <div class="input-group">
                        <label for="password">SENHA</label>
                        <div style="position: relative; width: 100%;">
                            <input type="password" id="password" name="password" required autocomplete="new-password" style="width: 100%; padding-right: 40px;">
                            <i class="fa-regular fa-eye toggle-password" data-target="password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666; font-size: 1.1rem;"></i>
                        </div>
                        @error('password') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- CAMPO: CONFIRMAR SENHA COM OLHINHO --}}
                    <div class="input-group">
                        <label for="password_confirmation">CONFIRMAR SENHA</label>
                        <div style="position: relative; width: 100%;">
                            <input type="password" id="password_confirmation" name="password_confirmation" required style="width: 100%; padding-right: 40px;">
                            <i class="fa-regular fa-eye toggle-password" data-target="password_confirmation" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666; font-size: 1.1rem;"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-marrom" style="margin-top: 20px;">CADASTRAR</button>

                    <p style="font-size: 0.85rem; text-align: center; margin-top: 15px;">
                        Já tem uma conta? <a href="{{ route('login') }}" class="forgot-link" style="text-decoration: underline;">Faça login</a>
                    </p>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cpfInput = document.getElementById('cpf');
            const telInput = document.getElementById('telefone');

            // ==========================================
            // SCRIPT DO OLHINHO DA SENHA
            // ==========================================
            const togglePasswordIcons = document.querySelectorAll('.toggle-password');

            togglePasswordIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    // Pega o ID do input que este ícone controla (password ou password_confirmation)
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);

                    // Alterna o tipo do input e o ícone
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash'); // Troca para o ícone com traço
                    } else {
                        input.type = 'password';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye'); // Volta para o olho normal
                    }
                });
            });

            // ==========================================
            // MÁSCARAS (CPF E TELEFONE)
            // ==========================================

            // Função Matemática para Validar CPF
            function isCPFValido(cpf) {
                cpf = cpf.replace(/[^\d]+/g, '');
                if (cpf.length !== 11 || !!cpf.match(/(\d)\1{10}/)) return false;
                let add = 0;
                for (let i = 0; i < 9; i++) add += parseInt(cpf.charAt(i)) * (10 - i);
                let rev = 11 - (add % 11);
                if (rev === 10 || rev === 11) rev = 0;
                if (rev !== parseInt(cpf.charAt(9))) return false;
                add = 0;
                for (let i = 0; i < 10; i++) add += parseInt(cpf.charAt(i)) * (11 - i);
                rev = 11 - (add % 11);
                if (rev === 10 || rev === 11) rev = 0;
                if (rev !== parseInt(cpf.charAt(10))) return false;
                return true;
            }

            // Máscara e Validação de CPF
            cpfInput.addEventListener('input', function(e) {
                let v = e.target.value.replace(/\D/g, ''); // Remove tudo que não é número
                if (v.length > 11) v = v.slice(0, 11);

                // Aplica a máscara visual
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                e.target.value = v;

                // Validação Real ao terminar de digitar
                if (v.length === 14) {
                    if (!isCPFValido(v)) {
                        this.style.borderBottomColor = 'red';
                        this.setCustomValidity('CPF inválido');
                    } else {
                        this.style.borderBottomColor = 'var(--color-brand)';
                        this.setCustomValidity('');
                    }
                }
            });

            // Máscara de Telefone
            telInput.addEventListener('input', function(e) {
                let v = e.target.value.replace(/\D/g, '');
                v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
                if (v.length <= 13) v = v.replace(/(\d{4})(\d)/, '$1-$2');
                else v = v.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = v;
            });
        });
    </script>
@endsection
