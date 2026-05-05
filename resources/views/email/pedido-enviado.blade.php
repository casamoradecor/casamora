<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Pedido foi Enviado - Casa MORÁ</title>
    <style>
        /* Importando a fonte Poppins */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #4B3621;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.8;
            font-size: 15px;
        }
        .tracking-box {
            background-color: #fafafa;
            border-left: 4px solid #D4AF37;
            padding: 20px;
            margin: 25px 0;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            letter-spacing: 1px;
            border-radius: 0 4px 4px 0;
        }
        .tracking-code {
            display: block;
            font-size: 22px;
            color: #4B3621;
            margin-top: 5px;
        }
        .btn {
            display: inline-block;
            padding: 14px 30px;
            background-color: #5B8C5A;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
        }
        .footer {
            background-color: #f1f1f1;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #888888;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>CASA MORÁ</h1>
    </div>

    <div class="content">
        <p>Olá, <strong>{{ explode(' ', $pedido->cliente->name ?? 'Cliente')[0] }}</strong>!</p>
        <p>Temos ótimas notícias! O seu pedido <strong>#{{ $pedido->id }}</strong> foi embalado com muito carinho, acabou de ser despachado e já está a caminho do seu endereço.</p>

        <div class="tracking-box">
            <span style="font-size: 14px; color: #666; text-transform: uppercase;">Código de Rastreamento</span>
            <span class="tracking-code">{{ $pedido->codigo_rastreio }}</span>
        </div>

        <p style="text-align: center;">
            <a href="{{ url('/meus-pedidos') }}" class="btn">Acompanhar meu Pedido</a>
        </p>

        <p style="margin-top: 30px;">Agradecemos por escolher a Casa MORÁ para decorar o seu lar. Se tiver qualquer dúvida, estamos à disposição!</p>

        <p>Com carinho,<br><strong>Equipe Casa MORÁ</strong></p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Casa MORÁ. Todos os direitos reservados.
    </div>
</div>
</body>
</html>
