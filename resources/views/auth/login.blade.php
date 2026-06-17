@extends('layouts.app')

@section('title', 'Casa MORÁ — Login')

{{-- Adiciona a classe scrolled no header --}}
@section('header_class', 'scrolled')

{{-- Adiciona o CSS específico do login no head --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
    <main class="login-page">
        <div class="login-split">
            <div class="login-image">
                <img src="{{ asset('assets/Embalagem.png') }}" alt="Ambiente Casa MORÁ">
            </div>

            <div class="login-content">
                <h2>CONTA</h2>

                {{-- Exibe mensagens de status (ex: link de senha enviado) --}}
                @if (session('status'))
                    <div style="color: green; margin-bottom: 15px; font-size: 0.9rem;">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Alterado para POST apontando para a rota de login do Breeze --}}
                <form class="login-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="input-group">
                        <label for="email">E-MAIL</label>
                        {{-- Adicionado name="email" e old('email') para não apagar ao errar a senha --}}
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                        {{-- Exibição de erro do e-mail --}}
                        @error('email')
                        <span style="color: red; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- CAMPO: SENHA COM OLHINHO --}}
                    <div class="input-group">
                        <label for="password">SENHA</label>
                        <div style="position: relative; width: 100%;">
                            {{-- Adicionado padding-right para o texto não encostar no ícone --}}
                            <input type="password" id="password" name="password" required autocomplete="current-password" style="width: 100%; padding-right: 40px;">
                            {{-- Ícone do FontAwesome --}}
                            <i class="fa-regular fa-eye toggle-password" data-target="password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666; font-size: 1.1rem;"></i>
                        </div>
                        {{-- Exibição de erro da senha --}}
                        @error('password')
                        <span style="color: red; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Botão de lembrar-me padrão do Laravel --}}
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                        <input type="checkbox" id="remember_me" name="remember" style="width: auto;">
                        <label for="remember_me" style="font-size: 0.85rem; margin: 0;">Lembrar de mim</label>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn btn-marrom">LOGIN</button>

                        <a href="{{ route('register') }}" class="btn btn-branco">CRIAR SUA CONTA</a>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">ESQUECEU A SENHA?</a>
                    @endif

                    <div style="margin-top: 25px; text-align: center;">
                        <p style="font-size: 0.75rem; color: #777;">
                            Ao prosseguir com o acesso, você concorda com a nossa
                            <a href="/politica-de-privacidade" style="color: #4B3621; text-decoration: underline;">Política de Privacidade</a>.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
@endpush
