<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Pedido foi Enviado - Casa MORÁ</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        /* Estilos básicos para mobile */
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; padding: 15px !important; }
            .content { padding: 20px !important; }
            .tracking-code { font-size: 18px !important; }
            .btn-action { width: 100% !important; box-sizing: border-box; text-align: center; }
        }
    </style>
</head>
<body style="background-color: #fdfaf8; font-family: 'Poppins', Arial, sans-serif; margin: 0; padding: 20px;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center">
                <div class="container" style="max-width: 600px; width: 100%; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    
                    <div class="content" style="padding: 40px 30px;">
                        <h1 style="text-align: center; color: #4B3621; letter-spacing: 3px; margin: 0 0 30px 0; font-size: 24px; text-transform: uppercase;">CASA MORÁ</h1>

                        <div style="line-height: 1.6; color: #555; font-size: 15px;">
                            <p>Olá, <strong>{{ explode(' ', $pedido->cliente->name ?? 'Cliente')[0] }}</strong>!</p>
                            <p>Temos ótimas notícias! O seu pedido <strong>#{{ $pedido->id }}</strong> foi embalado com muito carinho e já está a caminho do seu endereço.</p>

                            <div style="background-color: #fdfaf8; border-left: 4px solid #4B3621; padding: 20px; margin: 25px 0; text-align: center; border-radius: 0 4px 4px 0;">
                                <span style="font-size: 12px; color: #888; text-transform: uppercase; font-weight: bold; letter-spacing: 1px;">Código de Rastreamento</span>
                                <span class="tracking-code" style="display: block; font-size: 22px; color: #4B3621; font-weight: bold; margin-top: 5px;">{{ $pedido->codigo_rastreio }}</span>
                            </div>

                            <p style="text-align: center; margin-top: 30px;">
                                <a href="{{ url('/meus-pedidos') }}" class="btn-action" style="background: #4B3621; color: #ffffff; padding: 15px 25px; text-decoration: none; font-size: 13px; display: inline-block; border-radius: 4px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase;">ACOMPANHAR MEU PEDIDO</a>
                            </p>

                            <p style="margin-top: 40px;">Agradecemos por escolher a Casa MORÁ. Se tiver qualquer dúvida, estamos à disposição!</p>

                            <p style="margin-bottom: 0;">Com carinho,<br><strong>Equipe Casa MORÁ</strong></p>
                        </div>
                    </div>

                    <div style="text-align: center; background: #fafafa; padding: 20px; border-top: 1px solid #eee;">
                        <a href="{{ url('/') }}" style="color: #4B3621; font-weight: bold; text-decoration: none; font-size: 11px; letter-spacing: 1px; text-transform: uppercase;">VISITAR LOJA COMPLETA</a>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>