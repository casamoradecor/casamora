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
        <form class="login-form" onsubmit="event.preventDefault();">
          <div class="input-group">
            <label for="email">E-MAIL</label>
            <input type="email" id="email" required>
          </div>
          <div class="input-group">
            <label for="password">SENHA</label>
            <input type="password" id="password" required>
          </div>
          <button type="submit" class="btn-primary">LOGIN</button>
          <button type="button" class="btn-secondary">CRIAR SUA CONTA</button>
          <a href="#" class="forgot-link">ESQUECEU A SENHA?</a>
        </form>
      </div>
    </div>
  </main>
@endsection