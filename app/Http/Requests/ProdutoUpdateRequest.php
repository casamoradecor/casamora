<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdutoUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    // Tratamento da vírgula no preço ANTES de validar
    protected function prepareForValidation()
    {
        if ($this->has('preco')) {
            $this->merge([
                'preco' => str_replace(',', '.', $this->preco),
            ]);
        }
    }

    public function rules()
    {
        $id = $this->route('id') ?? $this->route('produto');

        return [
            'codigo'       => 'required|max:50|unique:produtos,codigo,' . $id,
            'nome'         => 'required|max:255',
            'preco'        => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagem'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'peso'         => 'required|numeric|min:0',
            'largura'      => 'required|integer|min:0',
            'altura'       => 'required|integer|min:0',
            'comprimento'  => 'required|integer|min:0',
            'estoque'      => 'required|integer|min:0',
            'descricao'    => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'codigo.unique' => 'Este código (SKU) já está em uso por outro produto.',
            'preco.min'     => 'O valor não pode ser negativo.',
            'peso.min'      => 'O peso não pode ser negativo.',
            'largura.min'   => 'A largura não pode ser negativa.',
            'altura.min'    => 'A altura não pode ser negativa.',
            'comprimento.min'=> 'O comprimento não pode ser negativo.',
            'estoque.min'   => 'O estoque não pode ser negativo.',
            'imagem.image'  => 'O arquivo deve ser uma imagem válida.'
        ];
    }
}
