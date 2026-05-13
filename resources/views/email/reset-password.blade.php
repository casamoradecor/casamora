<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha - Casa MORÁ</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; padding: 15px !important; }
            .content { padding: 20px !important; }
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
                            <p>Olá!</p>
                            <p>Recebemos uma solicitação para redefinir a senha da sua conta na Casa MORÁ. Clique no botão abaixo para prosseguir:</p>

                            <p style="text-align: center; margin: 35px 0;">
                                <a href="{{ $url }}" class="btn-action" style="background: #4B3621; color: #ffffff; padding: 15px 25px; text-decoration: none; font-size: 13px; display: inline-block; border-radius: 4px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase;">REDEFINIR MINHA SENHA</a>
                            </p>

                            <p style="font-size: 13px; color: #888; border-top: 1px solid #f1f1f1; padding-top: 20px;">
                                Este link expirará em 60 minutos. Se não solicitou esta alteração, pode ignorar este e-mail em segurança.
                            </p>

                            <p style="margin-bottom: 0;">Com carinho,<br><strong>Equipe Casa MORÁ</strong></p>
                        </div>
                    </div>

                    <div style="text-align: center; background: #fafafa; padding: 20px; border-top: 1px solid #eee;">
                        <a href="{{ url('/') }}" style="color: #4B3621; font-weight: bold; text-decoration: none; font-size: 11px; letter-spacing: 1px; text-transform: uppercase;">VOLTAR PARA A LOJA</a>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>