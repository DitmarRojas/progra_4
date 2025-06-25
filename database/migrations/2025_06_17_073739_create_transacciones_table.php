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
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_transaccion');
            $table->string('descripcion')->nullable();
            $table->string('num_referencia')->nullable();
            $table->enum('tipo_transaccion',['Ingreso', 'Gasto', 'Transferencia', 'Ajuste', 'Otro'])->default('Otro');
            $table->boolean('estado')->default(false);
            $table->foreignId('usuario_id')->nullable()
            ->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};
