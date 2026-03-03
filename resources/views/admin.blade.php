@extends('layouts.app')

@section('title', 'Casa MORÁ — Painel Administrativo')

@section('content')
<main style="padding: 100px 10%; background: #f9f9f9; min-height: 100vh;">
    <h1 style="text-transform: lowercase; margin-bottom: 40px; color: var(--color-brand); font-weight: 400;">painel administrativo</h1>

    @if(session('sucesso'))
        <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem;">
            {{ session('sucesso') }}
        </div>
    @endif

    <section style="margin-bottom: 50px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
        <h3 style="text-transform: lowercase; color: var(--color-sub); margin-bottom: 10px;">banner principal (hero)</h3>
        <p style="font-size: 0.75rem; color: #888; margin-bottom: 25px;">imagem atual: hero_banner.png</p>
        
        <form action="{{ route('admin.uploadBanner') }}" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 20px;">
            @csrf
            <input type="file" name="hero_img" required>
            <button type="submit" style="background: transparent; color: var(--color-brand); border: 1px solid var(--color-brand); padding: 10px 30px; border-radius: 50px; cursor: pointer; font-size: 0.75rem; font-weight: 600; text-transform: lowercase;">
                atualizar banner
            </button>
        </form>
    </section>

    <section style="margin-bottom: 50px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
        <h3 style="text-transform: lowercase; color: var(--color-sub); margin-bottom: 30px;">novo produto para o carrossel</h3>
        
        <div style="display: flex; gap: 60px; align-items: flex-start;">
            <div style="width: 250px; flex-shrink: 0;">
                <p style="font-size: 0.65rem; color: #aaa; margin-bottom: 15px; text-transform: lowercase;">visualização em tempo real:</p>
                <div style="border: 1px solid #f0f0f0; border-radius: 8px; overflow: hidden; background: white;">
                    <img id="previewImg" src="{{ asset('assets/vasomora.png') }}" style="width: 100%; height: 280px; object-fit: cover;">
                    <div style="padding: 15px; display: flex; justify-content: space-between; align-items: baseline;">
                        <span id="previewNome" style="font-size: 0.85rem; color: #666; text-transform: lowercase;">nome do item</span>
                        <span id="previewPreco" style="font-weight: 600; font-size: 0.9rem; color: var(--color-brand); text-transform: none !important;">R$ 0,00</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.produto.store') }}" method="POST" enctype="multipart/form-data" style="flex: 1; display: flex; flex-direction: column; gap: 20px;">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 0.7rem; font-weight: 700;">FOTO DO PRODUTO</label>
                    <input type="file" name="imagem" id="inputImagem" required onchange="previewFile()">
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 0.7rem; font-weight: 700;">NOME DO PRODUTO</label>
                    <input type="text" name="nome" id="inputNome" placeholder="ex: vaso morá minimalist" required onkeyup="updateTextPreview()" style="padding: 12px; border: 1px solid #eee; border-radius: 8px; outline: none;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 0.7rem; font-weight: 700;">CATEGORIA</label>
                    <select name="categoria_id" required style="padding: 12px; border: 1px solid #eee; border-radius: 8px; background: white;">
                        <option value="1">vasos</option>
                        <option value="2">utensílios</option>
                        <option value="3">decorações</option>
                    </select>
                </div>

                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1; display: flex; flex-direction: column; gap: 8px;">
                        <label style="font-size: 0.7rem; font-weight: 700;">PREÇO (R$)</label>
                        <input type="number" step="0.01" name="preco" id="inputPreco" placeholder="189.90" required onkeyup="updateTextPreview()" style="padding: 12px; border: 1px solid #eee; border-radius: 8px; outline: none;">
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column; gap: 8px;">
                        <label style="font-size: 0.7rem; font-weight: 700;">ESTOQUE INICIAL</label>
                        <input type="number" name="estoque" placeholder="10" required style="padding: 12px; border: 1px solid #eee; border-radius: 8px; outline: none;">
                    </div>
                </div>
                
                <button type="submit" style="background: var(--color-brand); color: #fff; border: none; padding: 15px; border-radius: 50px; cursor: pointer; font-weight: 700; text-transform: lowercase;">
                    cadastrar produto
                </button>
            </form>
        </div>
    </section>

    <section style="margin-bottom: 50px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
        <h3 style="text-transform: lowercase; color: var(--color-sub); margin-bottom: 30px;">gerenciar carrossel atual</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
            @foreach($produtos as $produto)
            <div style="border: 1px solid #eee; padding: 20px; border-radius: 12px; background: #fff;">
                <form action="{{ route('admin.produto.update', $produto->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    
                    <div style="position: relative; margin-bottom: 15px;">
                        <img src="{{ Storage::url($produto->imagem) }}" style="width: 100%; height: 180px; object-fit: cover; border-radius: 8px;">
                        <label style="position: absolute; bottom: 5px; right: 5px; background: rgba(255,255,255,0.9); padding: 5px; border-radius: 5px; cursor: pointer; font-size: 0.6rem;">
                            <i class="fa-solid fa-camera"></i> trocar foto
                            <input type="file" name="imagem" style="display: none;">
                        </label>
                    </div>

                    <input type="text" name="nome" value="{{ $produto->nome }}" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #eee; border-radius: 5px; font-size: 0.8rem;">

                    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <label style="font-size: 0.6rem; color: #aaa; text-transform: lowercase;">preço atual</label>
                            <input type="number" step="0.01" name="preco" value="{{ $produto->preco }}" style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 5px; font-size: 0.8rem;">
                        </div>
                        <div style="flex: 1;">
                            <label style="font-size: 0.6rem; color: #aaa; text-transform: lowercase;">qtd estoque</label>
                            <input type="number" name="estoque" value="{{ $produto->estoque }}" style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 5px; font-size: 0.8rem;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" style="flex: 2; background: #e3dcd2; border: none; padding: 10px; border-radius: 5px; font-size: 0.7rem; font-weight: 700; cursor: pointer; text-transform: lowercase;">salvar</button>
                </form>
                
                <form action="{{ route('admin.produto.destroy', $produto->id) }}" method="POST" onsubmit="return confirm('deseja realmente excluir este produto?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="background: #ffebee; color: #c62828; border: none; padding: 10px; border-radius: 50%; width: 35px; height: 35px; cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
                </form>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <section style="margin-bottom: 50px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
        <h3 style="text-transform: lowercase; color: var(--color-sub);">foto do ambiente (shoppable)</h3>
        <form action="{{ route('admin.updateShoppable') }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 20px;">
            @csrf
            <input type="file" name="shoppable_img" required>
            <button type="submit" style="background: var(--color-brand); color: #fff; border: none; padding: 10px 25px; border-radius: 50px; cursor: pointer;">atualizar ambiente</button>
        </form>
    </section>

    <section style="margin-bottom: 50px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
        <h3 style="text-transform: lowercase; color: var(--color-sub);">imagem de destaque (lado do carrossel)</h3>
        <form action="{{ route('admin.updateDestaque') }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 20px;">
            @csrf
            <input type="file" name="destaque_img" required>
            <button type="submit" style="background: var(--color-brand); color: #fff; border: none; padding: 10px 25px; border-radius: 50px; cursor: pointer;">atualizar destaque</button>
        </form>
    </section>

    <section style="margin-bottom: 50px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
        <h3 style="text-transform: lowercase; color: var(--color-sub);">imagens das categorias</h3>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px;">
            @foreach(['vasos' => 1, 'utensílios' => 2, 'decorações' => 3] as $nome => $id)
                <form action="{{ route('admin.updateCategoria', $id) }}" method="POST" enctype="multipart/form-data" style="border: 1px solid #eee; padding: 15px; border-radius: 10px; background: #fff;">
                    @csrf
                    <p style="font-size: 0.7rem; margin-bottom: 10px;">{{ $nome }}</p>
                    <input type="file" name="cat_img" required style="font-size: 0.7rem;">
                    <button type="submit" style="margin-top: 10px; width: 100%; font-size: 0.7rem; cursor: pointer; border: 1px solid #eee; background: none; padding: 5px; border-radius: 5px;">trocar foto</button>
                </form>
            @endforeach
        </div>
    </section>
</main>

<script>
    function previewFile() {
        const preview = document.getElementById('previewImg');
        const file = document.getElementById('inputImagem').files[0];
        const reader = new FileReader();
        reader.onloadend = () => { preview.src = reader.result; }
        if (file) { reader.readAsDataURL(file); }
    }

    function updateTextPreview() {
        const nome = document.getElementById('inputNome').value;
        const preco = document.getElementById('inputPreco').value;
        document.getElementById('previewNome').innerText = nome || 'nome do item';
        if(preco) {
            document.getElementById('previewPreco').innerText = 'R$ ' + parseFloat(preco).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        } else {
            document.getElementById('previewPreco').innerText = 'R$ 0,00';
        }
    }
</script>
@endsection