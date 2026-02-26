<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // 1. Atualiza a tabela pedidos existente
    Schema::table('pedidos', function (Blueprint $table) {
        // Adiciona apenas as colunas que faltam (exemplo se não existirem)
        if (!Schema::hasColumn('pedidos', 'nome_entrega')) {
            $table->string('nome_entrega')->after('status');
        }
        if (!Schema::hasColumn('pedidos', 'cpf_entrega')) {
            $table->string('cpf_entrega')->after('nome_entrega');
        }
        if (!Schema::hasColumn('pedidos', 'cep')) {
            $table->string('cep')->after('cpf_entrega');
        }
        if (!Schema::hasColumn('pedidos', 'endereco')) {
            $table->string('endereco')->after('cep');
        }
    });

    // 2. Cria a tabela de itens (se ela ainda não existir)
    if (!Schema::hasTable('pedido_itens')) {
        Schema::create('pedido_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained()->onDelete('cascade');
            $table->foreignId('produto_id')->constrained();
            $table->integer('quantidade');
            $table->decimal('preco_unitario', 10, 2);
            $table->timestamps();
        });
    }
}

public function down(): void
{
    // Opcional: remover colunas se der rollback
    Schema::table('pedidos', function (Blueprint $table) {
        $table->dropColumn(['nome_entrega', 'cpf_entrega', 'cep', 'endereco']);
    });
    Schema::dropIfExists('pedido_itens');
}
};
