<!DOCTYPE html>
<html>
<head>
    <style>
        .product-grid { display: table; width: 100%; border-spacing: 10px; }
        .product-card { display: table-cell; width: 50%; background: #ffffff; padding: 10px; text-align: center; border: 1px solid #eee; }
        .product-img { width: 100%; height: 180px; object-fit: cover; }
        .product-name { font-size: 14px; color: #4B3621; font-weight: bold; margin: 10px 0 5px; text-transform: uppercase; }
        .product-price { color: #888; font-size: 13px; font-weight: 800; }
        .btn-buy { background: #4B3621; color: #fff; padding: 8px 15px; text-decoration: none; font-size: 11px; display: inline-block; margin-top: 10px; border-radius: 4px; }
    </style>
</head>
<body style="background-color: #fdfaf8; font-family: 'Poppins', sans-serif; padding: 20px;">
<div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px;">

    <h1 style="text-align: center; color: #4B3621; letter-spacing: 3px;">CASA MORÁ</h1>

    <div style="margin-bottom: 30px; line-height: 1.6; color: #555;">
        {!! $mensagem !!}
    </div>

    <h3 style="text-align: center; text-transform: uppercase; letter-spacing: 2px; color: #4B3621; font-size: 15px;">Destaques para você</h3>

    <div class="product-grid">
        @foreach($produtos->chunk(2) as $par)
        <div style="display: table-row;">
            @foreach($par as $produto)
                <div class="product-card">
                    {{-- Importante: usar url() para o e-mail carregar a foto do seu servidor --}}
                    <img src="{{ url('storage/' . $produto->imagem) }}" class="product-img">
                    <div class="product-name">{{ $produto->nome }}</div>
                    <div class="product-price">R$ {{ number_format($produto->preco, 2, ',', '.') }}</div>
                    <a href="{{ url('/produto/' . $produto->id) }}" class="btn-buy">VER DETALHES</a>
                </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <div style="text-align: center; margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px;">
        <a href="{{ url('/') }}" style="color: #4B3621; font-weight: bold; text-decoration: none; font-size: 12px; letter-spacing: 1px;">VISITAR LOJA COMPLETA</a>
    </div>
</div>
</body>
</html>