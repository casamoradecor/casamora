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
        <img src="{{ asset('assets/vasomora.png') }}" alt="Ambiente Casa MORÁ">
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

          <div class="input-group">
            <label for="password">SENHA</label>
            {{-- Adicionado name="password" --}}
            <input type="password" id="password" name="password" required autocomplete="current-password">
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

          <button type="submit" class="btn-primary">LOGIN</button>
          
          {{-- Alterado para um link com aparência de botão apontando para a tela de registro --}}
          <a href="{{ route('register') }}" class="btn-secondary" style="display: flex; justify-content: center; align-items: center; text-decoration: none;">CRIAR SUA CONTA</a>
          
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forgot-link">ESQUECEU A SENHA?</a>
          @endif
        </form>
      </div>
    </div>
  </main>
@endsection