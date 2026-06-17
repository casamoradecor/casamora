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
                <img src="{{ asset('assets/embalagem_casamora.png') }}" alt="Ambiente Casa MORÁ">
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

                    {{-- TERMO DE PRIVACIDADE E CONSENTIMENTO RIGOROSO LGPD --}}
                    <div style="display: flex; align-items: flex-start; gap: 10px; margin-top: 20px;">
                        <input type="checkbox" id="lgpd_consent" name="lgpd_consent" required style="width: auto; margin-top: 3px;">
                        <label for="lgpd_consent" style="font-size: 0.8rem; color: #555; line-height: 1.3; font-weight: normal;">
                            Li e estou de acordo com os <a href="/termos-de-uso" style="color: #4B3621; text-decoration: underline;">Termos de Uso</a> e
                            <a href="/politica-de-privacidade" style="color: #4B3621; text-decoration: underline;">Política de Privacidade</a>.
                            Compreendo que meu CPF e telefone serão tratados para fins de faturamento, segurança e entrega logística das minhas compras.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-marrom" style="margin-top: 20px;">CADASTRAR</button>

                    <p style="font-size: 0.85rem; text-align: center; margin-top: 15px;">
                        Já tem uma conta? <a href="{{ route('login') }}" class="forgot-link" style="text-decoration: underline;">Faça login</a>
                    </p>
                </form>
            </div>
        </div>
    </main>
@endsection

@push('js')
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
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);

                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    }
                });
            });

            // ==========================================
            // MÁSCARAS (CPF E TELEFONE)
            // ==========================================
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

            cpfInput.addEventListener('input', function(e) {
                let v = e.target.value.replace(/\D/g, '');
                if (v.length > 11) v = v.slice(0, 11);

                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                e.target.value = v;

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

            telInput.addEventListener('input', function(e) {
                let v = e.target.value.replace(/\D/g, '');
                v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
                if (v.length <= 13) v = v.replace(/(\d{4})(\d)/, '$1-$2');
                else v = v.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = v;
            });
        });
    </script>
@endpush
