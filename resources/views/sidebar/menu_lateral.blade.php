<aside class="admin-sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('assets/ICONE RGB.png') }}" alt="Casa MORÁ">
        <span class="sidebar-logo-text">casa morá</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('admin.index') }}" class="nav-item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> início
        </a>
        <a href="#" class="nav-item">
            <i class="fa-solid fa-chart-line"></i> estatísticas
        </a>
        <a href="{{ route('admin.newsletter.index') }}" class="nav-item {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
            <i class="fa-solid fa-envelope"></i> newsletter
        </a>

        <div class="nav-group-title">Gestão</div>
        <a href="{{ route('admin.categorias.index') }}" class="nav-item {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}">
            <i class="fa-solid fa-layer-group"></i> categorias
        </a>
        <a href="{{ route('admin.produtos.create') }}" class="nav-item {{ request()->routeIs('admin.produtos.*') ? 'active' : '' }}">
            <i class="fa-solid fa-box"></i> produtos
        </a>
        <a href="{{ route('admin.vendas.index') }}" class="nav-item">
            <i class="fa-solid fa-receipt"></i> vendas
        </a>

        <div class="nav-group-title">Personalização</div>
        <a href="{{ route('admin.visual.edit') }}" class="nav-item {{ request()->routeIs('admin.visual.*') ? 'active' : '' }}">
            <i class="fa-solid fa-palette"></i> visual da loja
        </a>
        <a href="{{ route('admin.sobre.edit') }}" class="nav-item {{ request()->routeIs('admin.sobre.*') ? 'active' : '' }}">
            <i class="fa-solid fa-address-card"></i> sobre nós
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="/" class="nav-item nav-item-back">
            <i class="fa-solid fa-arrow-left"></i> voltar ao site
        </a>
    </div>
</aside>