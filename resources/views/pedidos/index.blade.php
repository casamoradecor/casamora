@extends('layouts.app')

@section('title', 'MEUS PEDIDOS — CASA MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <main class="dashboard-container">
        <aside class="dashboard-nav">
            <a href="{{ route('dashboard') }}" style="text-decoration: underline;">RESUMO</a>
            <a href="{{ route('pedidos.index') }}">MEUS PEDIDOS</a>
            <a href="{{ route('enderecos.index') }}">ENDEREÇOS</a>
            <a href="{{ route('perfil.edit') }}">EDITAR PERFIL</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">SAIR DA CONTA</button>
            </form>
        </aside>

        <section class="dashboard-content">
            <h2>MEUS PEDIDOS</h2>
            <p>ACOMPANHE ABAIXO O HISTÓRICO DE TODAS AS SUAS COMPRAS REALIZADAS NA CASA MORÁ.</p>

            @if($pedidos->isEmpty())
                <div class="info-card">
                    <p>VOCÊ AINDA NÃO REALIZOU NENHUM PEDIDO.</p>
                </div>
            @else
                <table class="tabela-pedidos">
                    <thead>
                    <tr>
                        <th>PEDIDO</th>
                        <th>DATA</th>
                        <th>STATUS</th>
                        <th>TOTAL</th>
                        <th style="text-align: center;">AÇÕES</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pedidos as $p)
                        <tr>
                            <td><strong>#{{ $p->codigo_externo }}</strong></td>
                            <td>{{ $p->created_at->format('d/m/Y') }}</td>
                            <td>
                            <span class="status-tag status-{{ $p->status }}">
                                {{ $p->status == 'approved' ? 'PAGO' : 'PENDENTE' }}
                            </span>
                            </td>
                            <td><strong>R$ {{ number_format($p->valor_total, 2, ',', '.') }}</strong></td>
                            <td style="text-align: center;">
                                <a href="{{ route('pedidos.show', $p->id) }}" class="link-tabela">
                                    VER DETALHES
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </main>
@endsection
