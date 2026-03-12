<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            // Remove a coluna antiga
            $table->dropColumn('codigo_referencia');

            // Adiciona as colunas de frete (unidades em cm e kg)
            $table->decimal('peso', 8, 3)->default(0)->after('preco'); // Peso em kg (ex: 1.500)
            $table->integer('largura')->default(0)->after('peso');     // em cm
            $table->integer('altura')->default(0)->after('largura');   // em cm
            $table->integer('comprimento')->default(0)->after('altura'); // em cm
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
