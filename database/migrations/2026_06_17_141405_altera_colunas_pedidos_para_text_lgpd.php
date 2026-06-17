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
        // Altera as colunas para o tipo TEXT, permitindo o armazenamento dos hashes longos da LGPD
        Schema::table('pedidos', function (Blueprint $table) {
            $table->text('nome_entrega')->change();
            $table->text('cpf_entrega')->change();
            $table->text('cep')->change();
            $table->text('endereco')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverte para VARCHAR(255) caso precise desfazer a migration
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('nome_entrega')->change();
            $table->string('cpf_entrega')->change();
            $table->string('cep')->change();
            $table->string('endereco')->change();
        });
    }
};
