@extends('layouts.app')

@section('title', 'Recuperar Senha — Casa MORÁ')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        .forgot-wrapper {
            font-family: 'Poppins', sans-serif;
            padding: 100px 20px;
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .forgot-card {
            background: #fff;
            max-width: 550px;
            width: 100%;
            padding: 60px;
            border-radius: 16px;
            text-align: center;
        }

        .forgot-title {
            color: #4B3621;
            font-weight: 600;
            font-size: 1.8rem;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .forgot-text {
            color: #666;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 40px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 25px;
        }

        .form-label {
            color: #4B3621;
            font-weight: 600;
            font-size: 0.8rem;
            display: block;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s ease;
            font-size: 1rem;
            background-color: #fafafa;
            box-sizing: border-box;
            color: #4B3621;
        }


        .status-message {
            background-color: #f1f5f1;
            color: #3e5c3e;
            border: 1px solid #d4ddd4;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
        }

        .error-message {
            color: #A63D40;
            font-size: 0.85rem;
            margin-top: 8px;
            display: block;
        }

        @media (max-width: 768px) {
            .forgot-wrapper { padding: 50px 15px; }
            .forgot-card { padding: 40px 25px; border-radius: 12px; }
            .forgot-title { font-size: 1.4rem; }
            .forgot-text { font-size: 0.9rem; margin-bottom: 30px; }
        }
    </style>

    <div class="forgot-wrapper">
        <div class="forgot-card">
            <h1 class="forgot-title">Recuperar Senha</h1>
            <p class="forgot-text">
                Esqueceu sua senha? Não se preocupe. <br>
                Informe seu e-mail abaixo e enviaremos as instruções para você criar uma nova senha.
            </p>

            @if (session('status'))
                <div class="status-message">
                    @if(session('status') == 'We have emailed your password reset link.')
                        Enviamos o link de recuperação para o seu e-mail.
                    @else
                        {{ session('status') }}
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Seu e-mail de cadastro</label>
                    <input id="email" type="email" name="email" class="form-input"
                           value="{{ old('email') }}" placeholder="exemplo@email.com"
                           required autofocus>
                    @error('email')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-marrom">
                    Enviar Instruções
                </button>
            </form>
        </div>
    </div>
@endsection