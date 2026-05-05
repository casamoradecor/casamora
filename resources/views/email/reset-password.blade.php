<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Fallback de fonte para garantir que não quebre em nenhum dispositivo */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #fcfaf8;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #fcfaf8;
            padding: 40px 0;
        }
        .main-table {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #4B3621; /* Marrom Casa MORÁ */
            padding: 50px 20px;
            text-align: center;
        }
        .logo-text {
            color: #ffffff !important; /* Texto em Branco Puro - Muito mais chique */
            font-size: 26px;
            font-weight: 600;
            letter-spacing: 5px;
            text-transform: uppercase;
            text-decoration: none;
        }
        .content {
            padding: 50px 40px;
            text-align: center;
            color: #4B3621;
        }
        h1 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        p {
            line-height: 1.8;
            font-size: 15px;
            color: #666666;
            margin-bottom: 30px;
        }
        .button-container {
            padding: 10px 0 30px 0;
        }
        .btn-mora {
            background-color: #4B3621;
            color: #ffffff !important;
            padding: 18px 35px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 2px;
            display: inline-block;
        }
        .footer {
            padding: 30px;
            text-align: center;
            color: #aaaaaa;
            font-size: 11px;
            letter-spacing: 1px;
        }
        .divider {
            border-top: 1px solid #eeeeee;
            margin: 0 40px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <table class="main-table">
        <tr>
            <td class="header">
                <div class="logo-text">CASA MORÁ</div>
            </td>
        </tr>

        <tr>
            <td class="content">
                <h1>Recuperação de Acesso</h1>
                <p>Olá, {{ $name }}.<br>
                    Recebemos uma solicitação para redefinir a senha da sua conta. Para prosseguir com a criação de uma nova senha, clique no botão abaixo:</p>

                <div class="button-container">
                    <a href="{{ $url }}" class="btn-mora">Redefinir Senha</a>
                </div>

                <p style="font-size: 12px; color: #999; margin-top: 20px;">
                    Este link é válido por 60 minutos.<br>
                    Se você não solicitou esta alteração, ignore este e-mail.
                </p>
            </td>
        </tr>

        <tr>
            <td><div class="divider"></div></td>
        </tr>

        <tr>
            <td class="footer">
                &copy; {{ date('Y') }} CASA MORÁ<br>
                Este é um e-mail automático, por favor não responda.
            </td>
        </tr>
    </table>
</div>
</body>
</html>
