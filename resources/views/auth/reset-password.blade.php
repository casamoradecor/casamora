@extends('layouts.app')

@section('title', 'Definir Nova Senha — Casa MORÁ')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        .reset-wrapper {
            font-family: 'Poppins', sans-serif;
            padding: 100px 20px;
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reset-card {
            background: #fff;
            max-width: 550px;
            width: 100%;
            padding: 60px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(75, 54, 33, 0.05);
            text-align: center;
        }

        .reset-title {
            color: #4B3621;
            font-weight: 600;
            font-size: 1.8rem;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .reset-text {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 35px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            color: #4B3621;
            font-weight: 600;
            font-size: 0.75rem;
            display: block;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            padding: 14px 45px 14px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s ease;
            font-size: 0.95rem;
            background-color: #fafafa;
            box-sizing: border-box;
            color: #4B3621;
        }

        .form-input:focus {
            border-color: #4B3621;
            background-color: #fff;
            box-shadow: none !important;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            color: #4B3621;
            cursor: pointer;
            font-size: 1.1rem;
            opacity: 0.7;
            transition: opacity 0.3s;
            z-index: 10;
        }

        .toggle-password:hover {
            opacity: 1;
        }

        .error-message {
            color: #A63D40;
            font-size: 0.8rem;
            margin-top: 5px;
            display: block;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .reset-wrapper { padding: 40px 15px; }
            .reset-card { padding: 40px 25px; }
            .reset-title { font-size: 1.4rem; }
        }
    </style>

    <div class="reset-wrapper">
        <div class="reset-card">
            <h1 class="reset-title">Nova Senha</h1>
            <p class="reset-text">Crie uma nova senha segura para acessar sua conta na Casa MORÁ.</p>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" type="email" name="email" class="form-input"
                           value="{{ old('email', $request->email) }}" required readonly style="background-color: #eee; cursor: not-allowed;">
                    @error('email')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Nova Senha</label>
                    <div class="input-container">
                        <input id="password" type="password" name="password" class="form-input"
                               required autofocus placeholder="Mínimo 8 caracteres, números e símbolos">
                        <i class="fa-regular fa-eye toggle-password" data-target="password"></i>
                    </div>
                    @error('password')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                    <div class="input-container">
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-input" required placeholder="Repita a senha">
                        <i class="fa-regular fa-eye toggle-password" data-target="password_confirmation"></i>
                    </div>
                    @error('password_confirmation')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-marrom">
                    Atualizar Senha
                </button>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(icon => {
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
    </script>
@endsection