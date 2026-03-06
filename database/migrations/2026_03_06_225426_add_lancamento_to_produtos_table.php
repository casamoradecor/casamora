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
    Schema::table('produtos', function (Blueprint $table) {
        // Adiciona o campo como falso por padrão
        $table->boolean('lancamento')->default(false)->after('categoria_id');
    });
}

public function down(): void
{
    Schema::table('produtos', function (Blueprint $table) {
        $table->dropColumn('lancamento');
    });
}
};
