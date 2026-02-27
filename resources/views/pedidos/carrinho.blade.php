@extends('layouts.app')

@section('title', 'Casa MORÁ — Meu Carrinho')

@section('content')
<main class="carrinho-container" style="padding: 50px 10%;">
    <h2>Meu Carrinho</h2>

    @if(session('carrinho') && count(session('carrinho')) > 0)
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                    <th style="padding: 10px;">Produto</th>
                    <th>Preço</th>
                    <th>Qtd</th>
                    <th>Subtotal</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                
                @foreach(session('carrinho') as $id => $item)
                    @php $total += $item['preco'] * $item['quantidade']; @endphp
                    
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; display: flex; align-items: center; gap: 15px;">
                            <img src="{{ asset('assets/vasomora.png') }}" width="60" alt="{{ $item['nome'] }}">
                            {{ $item['nome'] }}
                        </td>
                        <td>R$ {{ number_format($item['preco'], 2, ',', '.') }}</td>
                        <td>{{ $item['quantidade'] }}</td>
                        <td>R$ {{ number_format($item['preco'] * $item['quantidade'], 2, ',', '.') }}</td>
                        <td>
                            <form action="#" method="POST">
                                @csrf
                                <button type="submit" style="color: red; background: none; border: none; cursor: pointer;">
                                    Remover
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: right;">
            <h3>Total da Compra: R$ {{ number_format($total, 2, ',', '.') }}</h3>
            
            <div style="margin-top: 20px; display: flex; gap: 15px; justify-content: flex-end;">
                <a href="{{ route('home') }}" style="padding: 10px 20px; text-decoration: none; color: #4b2b22; border: 1px solid #4b2b22;">
                    Continuar Comprando
                </a>
                <a href="#" style="padding: 10px 20px; text-decoration: none; color: #fff; background-color: #4b2b22;">
                    Finalizar Compra
                </a>
            </div>
        </div>
    @else
        <div style="margin-top: 30px; text-align: center;">
            <p>Seu carrinho está vazio.</p>
            <a href="{{ route('home') }}" style="display: inline-block; margin-top: 15px; padding: 10px 20px; text-decoration: none; color: #fff; background-color: #4b2b22;">
                Voltar para a loja
            </a>
        </div>
    @endif
</main>
@endsection